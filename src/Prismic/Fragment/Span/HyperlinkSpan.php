<?hh // strict

/*
 * This file is part of the Prismic PHP SDK
 *
 * Copyright 2013 Zengularity (http://www.zengularity.com).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prismic\Fragment\Span;

use Prismic\Fragment\Link\LinkInterface;

class HyperlinkSpan implements SpanInterface
{

    private int $start;
    private int $end;
    private LinkInterface $link;

    public function __construct(int $start, int $end, LinkInterface $link)
    {
        $this->start = $start;
        $this->end = $end;
        $this->link = $link;
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function getEnd(): int
    {
        return $this->end;
    }

    public function getLink(): LinkInterface
    {
        return $this->link;
    }
}
