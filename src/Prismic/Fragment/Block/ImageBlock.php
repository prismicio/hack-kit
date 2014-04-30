<?hh

/*
 * This file is part of the Prismic hack SDK
 *
 * Copyright 2013 Zengularity (http://www.zengularity.com).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prismic\Fragment\Block;

use Prismic\Fragment\ImageView;

class ImageBlock implements BlockInterface
{

    private $view;

    public function __construct(ImageView $view)
    {
        $this->view = $view;
    }

    public function getView(): ImageView
    {
        return $this->view;
    }
}
