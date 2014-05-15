<?hh // strict

/*
 * This file is part of the Prismic hack SDK
 *
 * Copyright 2013 Zengularity (http://www.zengularity.com).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prismic\Fragment\Link;

use Prismic\LinkResolver;

class WebLink implements LinkInterface
{
    private string $url;
    private ?string $maybeContentType;

    public function __construct(string $url, ?string $maybeContentType = null)
    {
        $this->url = $url;
        $this->maybeContentType = $maybeContentType;
    }

    public function asHtml(?LinkResolver $linkResolver = null): string
    {
        return '<a href="' . $this->url . '">$url</a>';
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getContentType(): ?string
    {
        return $this->maybeContentType;
    }

    public static function parse(ImmMap<string, mixed> $json): WebLink
    {
        return new WebLink((string)$json->at('url'));
    }
}
