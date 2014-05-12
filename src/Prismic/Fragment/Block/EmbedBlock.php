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

use Prismic\Fragment\Embed;

class EmbedBlock implements BlockInterface
{
    private Embed $obj;

    public function __construct(Embed $obj)
    {
        $this->obj = $obj;
    }

    public function getObj(): Embed
    {
        return $this->obj;
    }
}
