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

class MediaLink implements LinkInterface
{
    private string $url;
    private string $kind;
    private int $size;
    private string $filename;

    public function __construct(string $url, string $kind, int $size, string $filename)
    {
        $this->url = $url;
        $this->kind = $kind;
        $this->size = $size;
        $this->filename = $filename;
    }

    public function asHtml(?LinkResolver $linkResolver = null): string
    {
        return '<a href="' . $this->url . '">' . $this->filename . '</a>';
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getKind(): string
    {
        return $this->kind;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public static function parse($json): MediaLink
    {
        return new MediaLink(
            $json->file->url,
            $json->file->kind,
            (int)$json->file->size,
            $json->file->name
        );
    }
}
