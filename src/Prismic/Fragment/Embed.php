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

use Prismic\LinkResolver;

class Embed implements FragmentInterface
{

    private string $type;
    private string $provider;
    private string $url;
    private ?int $maybeWidth;
    private ?int $maybeHeight;
    private ?string $maybeHtml;
    private ImmMap<string, mixed> $oembedJson;

    public function __construct(
        string $type,
        string $provider,
        string $url,
        ?int $maybeWidth,
        ?int $maybeHeigth,
        ?string $maybeHtml,
        ImmMap<string, mixed> $oembedJson
    ) {
        $this->type = $type;
        $this->provider = $provider;
        $this->url = $url;
        $this->maybeWidth = $maybeWidth;
        $this->maybeHeight = $maybeHeigth;
        $this->maybeHtml = $maybeHtml;
        $this->oembedJson = $oembedJson;
    }

    public function asHtml(?LinkResolver $linkResolver = null): string
    {
        $type = (string)strtolower($this->type);
        $provider = (string)strtolower($this->provider);
        if (!is_null($this->maybeHtml)) {
            return '<div data-oembed="' . $this->url . '" data-oembed-type="' .
                    $type . '" data-oembed-provider="' . $provider . '">' . $this->maybeHtml . '</div>';
        } else {
            return "";
        }
    }

    public static function parse(ImmMap<string, mixed> $json): Embed
    {
        $oembed = \Prismic\Tools::requireImmMap($json->at('oembed'));
        $width = $oembed->get('width');
        $height = $oembed->get('height');
        $html = $oembed->get('html');

        return new Embed(
            (string)$oembed->at('type'),
            (string)$oembed->at('provider_name'),
            (string)$oembed->at('embed_url'),
            !is_null($width) ? (int)$width : null,
            !is_null($height) ? (int)$height : null,
            !is_null($html) ? (string)$html : null,
            $oembed
        );
    }
}
