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

class Color implements FragmentInterface
{
    private string $hex;

    public function __construct(string $hex)
    {
        $this->hex = $hex;
    }

    public function asHtml(?LinkResolver $linkResolver = null): string
    {
        return '<span class="color">' . $this->hex . '</span>';
    }

    public function getHexValue(): string
    {
        return $this->hex;
    }
}
