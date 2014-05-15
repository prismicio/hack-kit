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

class Date implements FragmentInterface
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function asHtml(?LinkResolver $linkResolver = null): string
    {
        return '<time>' . $this->value . '</time>';
    }

    public function getValue(): string {
        return $this->value;
    }

    public function asText(): string
    {
        return $this->value;
    }

    public function formatted(string $pattern): string
    {
        return date($pattern, $this->asEpoch());
    }

    public function asEpoch(): int
    {
        // UNSAFE
        return strtotime($this->value);
    }

}
