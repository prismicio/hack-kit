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

class Number implements FragmentInterface
{
    private float $value;

    public function __construct(float $value)
    {
        $this->value = $value;
    }

    public function asText(string $pattern): string
    {
        return sprintf($pattern, $this->value);
    }

    public function asInt(): int {
        return (int)$this->value;
    }

    public function asHtml(?LinkResolver $linkResolver = null): string
    {
        return '<span class="number">' . $this->value . '</span>';
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
