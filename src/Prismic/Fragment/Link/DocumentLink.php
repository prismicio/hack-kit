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

use Prismic\Fragment\Link\LinkInterface;
use Prismic\LinkResolver;

class DocumentLink implements LinkInterface
{
    private string $id;
    private string $type;
    private ImmVector<string> $tags;
    private string $slug;
    private bool $isBroken;

    public function __construct(string $id, string $type, ImmVector<string> $tags, string $slug, bool $isBroken)
    {
        $this->id = $id;
        $this->type = $type;
        $this->tags = $tags;
        $this->slug = $slug;
        $this->isBroken = $isBroken;
    }

    public function asHtml(?LinkResolver $linkResolver = null): string
    {
        if($linkResolver) {
            $href = $linkResolver->resolve($this);
        } else {
            return $href = "#";
        }
        return '<a href="' . $href . '">' . $this->slug . '</a>';
    }

    public static function parse(ImmMap<string, mixed> $json): DocumentLink
    {
        $document = \Prismic\Tools::requireImmMap($json);
        $tags = is_null($document->at('tags')) ? \Prismic\Tools::requireImmVector($document->at('tags')) : ImmVector {};
        return new DocumentLink(
            (string)$document->at('id'),
            (string)$document->at('type'),
            $tags,
            (string)$document->at('slug'),
            (bool)$json->at('isBroken')
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTags(): ImmVector<string>
    {
        return $this->tags;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function isBroken(): bool
    {
        return $this->isBroken;
    }
}
