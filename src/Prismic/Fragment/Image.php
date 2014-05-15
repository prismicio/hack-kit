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
use Prismic\Fragment\ImageView;

class Image implements FragmentInterface
{
    private ImageView $main;
    private ImmMap<string, ImageView> $views;

    public function __construct(ImageView $main, ImmMap<string, ImageView> $views = ImmMap {})
    {
        $this->main = $main;
        $this->views = $views;
    }

    public function asHtml(?LinkResolver $linkResolver = null): string
    {
        return $this->main->asHtml();
    }

    public function getView(string $key): ?ImageView
    {
        if (strtolower($key) == "main") {
            return $this->main;
        }

        return $this->views->get($key);
    }

    public function getMain(): ImageView
    {
        return $this->main;
    }

    public function getViews(): ImmMap<string, ImageView>
    {
        return $this->views;
    }
}
