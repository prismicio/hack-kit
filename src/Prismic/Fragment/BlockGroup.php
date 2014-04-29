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

use Prismic\Fragment\Block\BlockInterface;

class BlockGroup
{
    private $maybeTag;
    private $blocks;

    public function __construct(?string $maybeTag, ImmVector<BlockInterface> $blocks)
    {
        $this->maybeTag = $maybeTag;
        $this->blocks = $blocks;
    }

    public function addBlock($block): BlockGroup
    {
        $updated = $this->blocks->toVector()->add($block);
        return new BlockGroup($this->maybeTag, $updated->toImmVector());
    }

    public function getTag(): ?string
    {
        return $this->maybeTag;
    }

    public function getBlocks(): ImmVector<BlockInterface>
    {
        return $this->blocks;
    }
}
