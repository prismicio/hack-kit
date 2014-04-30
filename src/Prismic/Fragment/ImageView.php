<?hh

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
    private $url;
    private $alt;
    private $copyright;
    private $width;
    private $height;

    public function __construct(string $url, string $alt, string $copyright, int $width, int $height)
    {
        $this->url = $url;
        $this->alt = $alt;
        $this->copyright = $copyright;
        $this->width = $width;
        $this->height = $height;
    }

    public function asHtml(?LinkResolver $linkResolver = null, $attributes = array())
    {
        $doc = new DOMDocument();
        $img = $doc->createElement('img');
        $attributes = array_merge(array(
            'src' => $this->getUrl(),
            'alt' => $this->getAlt(),
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
        ), $attributes);
        foreach ($attributes as $key => $value) {
            $img->setAttribute($key, $value);
        }
        $doc->appendChild($img);
        return trim($doc->saveHTML());// trim removes trailing newline
    }

    public function ratio(): double
    {
        return $this->width / $this->height;
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

    public static function parse($json): ImageView
    {
        return new ImageView(
            $json->url,
            $json->alt,
            $json->copyright,
            $json->dimensions->width,
            $json->dimensions->height
        );
    }
}
