<?hh // strict

/*
 * This file is part of the Prismic hack SDK
 *
 * Copyright 2013 Zengularity (http://www.zengularity.com).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prismic\Fragment;

use DOMDocument;
use Prismic\LinkResolver;

class ImageView
{
    private string $url;
    private string $alt;
    private string $copyright;
    private int $width;
    private int $height;

    public function __construct(string $url, string $alt, string $copyright, int $width, int $height)
    {
        $this->url = $url;
        $this->alt = $alt;
        $this->copyright = $copyright;
        $this->width = $width;
        $this->height = $height;
    }

    public function asHtml(?LinkResolver $linkResolver = null): string
    {
        $doc = new DOMDocument();
        $img = $doc->createElement('img');
        $attributes = array(
            'src' => $this->getUrl(),
            'alt' => htmlentities($this->getAlt()),
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
        );
        foreach ($attributes as $key => $value) {
            $img->setAttribute($key, $value);
        }
        $doc->appendChild($img);
        return trim($doc->saveHTML());
    }

    public function ratio(): float
    {
        return (float)$this->width / (float)$this->height;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getAlt(): string
    {
        return $this->alt;
    }

    public function getCopyright(): string
    {
        return $this->copyright;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public static function parse(ImmMap<string, mixed> $json): ImageView
    {
        $dimensions = \Prismic\Tools::requireImmMap($json->at('dimensions'));
        return new ImageView(
            (string)$json->at('url'),
            (string)$json->at('alt'),
            (string)$json->at('copyright'),
            (int)$dimensions->at('width'),
            (int)$dimensions->at('height')
        );
    }
}
