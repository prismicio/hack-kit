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
    private \stdClass $oembedJson;

    public function __construct(
        string $type,
        string $provider,
        string $url,
        ?int $maybeWidth,
        ?int $maybeHeigth,
        ?string $maybeHtml,
        \stdClass $oembedJson
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
        if (isset($this->maybeHtml)) {
            return '<div data-oembed="' . $this->url . '" data-oembed-type="' .
                    strtolower($this->type) . '" data-oembed-provider="' .
                    strtolower($this->provider) . '">' . $this->maybeHtml . '</div>';
        } else {
            return "";
        }
    }

    public static function parse(\stdClass $json): Embed
    {
        return new Embed(
            $json->oembed->type,
            $json->oembed->provider_name,
            $json->oembed->embed_url,
            (int)$json->oembed->width,
            (int)$json->oembed->height,
            $json->oembed->html,
            $json->oembed
        );
    }
}
