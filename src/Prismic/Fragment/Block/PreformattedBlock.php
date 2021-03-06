<?hh // strict

/*
 * This file is part of the Prismic hack SDK
 *
 * Copyright 2013 Zengularity (http://www.zengularity.com).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prismic\Fragment\Block;

use Prismic\Fragment\Span\SpanInterface;

class PreformattedBlock implements TextInterface
{
    private string $text;
    private ImmVector<SpanInterface> $spans;

    public function __construct(string $text, ImmVector<SpanInterface> $spans = ImmVector {})
    {
        $this->text = $text;
        $this->spans = $spans;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getSpans(): ImmVector<SpanInterface>
    {
        return $this->spans;
    }
}
