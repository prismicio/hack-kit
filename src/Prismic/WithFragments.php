<?hh

/*
 * This file is part of the Prismic hack SDK
 *
 * Copyright 2013 Zengularity (http://www.zengularity.com).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prismic;

use Prismic\Fragment\FragmentInterface;
use Prismic\Fragment\Number;
use Prismic\Fragment\Color;
use Prismic\Fragment\Text;
use Prismic\Fragment\Date;
use Prismic\Fragment\Image;
use Prismic\Fragment\ImageView;
use Prismic\Fragment\StructuredText;
use Prismic\Fragment\Group;
use Prismic\Fragment\Block\ImageBlock;
use Prismic\Fragment\Block\TextInterface;
use Prismic\LinkResolver;

trait WithFragments {
    require implements WithFragmentsInterface;

    public function has(string $field): bool
    {
        return $this->fragments->containsKey($field);
    }

    public function get(string $field): ?FragmentInterface {
        $maybeFragment = $this->fragments()->get($field);
        if(isset($maybeFragment)) {
            return $maybeFragment;
        } else {
            return $this->getAll($field)->get(0);
        }
    }

    public function getAll(string $field): ImmVector<Fragment> {
        $indexedKey = '/^([^\[]+)(\[\d+\])?$/';
        return $this->fragments()
                    ->filterWithKey(($key, $value) ==> {
                        if (preg_match($indexedKey, $key, $groups) == 1) {
                            return isset($groups) && $groups[1] == $field;
                        } else {
                            return false;
                        }
                      })
                    ->values()
                    ->toImmVector();
    }

    public function getText(string $field): ?string
    {
        $fragment = $this->get($field);
        if (isset($fragment) && $fragment instanceof StructuredText) {
            $text = "";
            foreach ($fragment->getBlocks() as $block) {
                if ($block instanceof TextInterface) {
                    $text = $text . $block->getText();
                    $text = $text . "\n";
                }
            }

            return trim($text);
        } elseif (isset($fragment) && $fragment instanceof Number) {
            return (string)$fragment->getValue();
        } elseif (isset($fragment) && $fragment instanceof Color) {
            return $fragment->getHex();
        } elseif (isset($fragment) && $fragment instanceof Text) {
            return $fragment->getValue();
        } elseif (isset($fragment) && $fragment instanceof Date) {
            return $fragment->getValue();
        }

        return "";
    }

    public function getNumber(string $field): ?Number
    {
        $fragment = $this->get($field);
        if (isset($fragment) && $fragment instanceof Number) {
            return $fragment;
        }

        return null;
    }

    public function getNumberAs(string $field, string $pattern): ?string
    {
        $fragment = $this->get($field);
        if (isset($fragment) && $fragment instanceof Number) {
            if (isset($pattern) && isset($fragment)) {
                return $fragment->asText($pattern);
            }
        }

        return null;
    }

    public function getBoolean(string $field): ?bool
    {
        $fragment = $this->get($field);
        if (isset($fragment) && $fragment instanceof Text) {
            $value = strtolower($fragment->getValue());
            return in_array(strtolower($fragment->getValue()), array(
                'yes',
                'true',
            ));
        }

        return null;
    }

    public function getDate(string $field): ?Date
    {
        $fragment = $this->get($field);
        if (isset($fragment) && $fragment instanceof Date) {
            return $fragment;
        }

        return null;
    }

    public function getDateAs(string $field, ?string $pattern = null): ?string
    {
        $fragment = $this->get($field);
        if (isset($fragment) && $fragment instanceof Date) {
            if (isset($pattern)) {
                return $fragment->formatted($pattern);
            }
        }

        return null;
    }

    public function getHtml(string $field, ?LinkResolver $linkResolver = null)
    {
        $fragment = $this->get($field);
        if (isset($fragment) && method_exists($fragment, 'asHtml')) {
            return $fragment->asHtml($linkResolver);
        }

        return "";
    }

    public function getImage(string $field): ?Image
    {
        $fragment = $this->get($field);
        if (isset($fragment) && $fragment instanceof Image) {
            return $fragment;
        } elseif (isset($fragment) && $fragment instanceof StructuredText) {
            foreach ($fragment->getBlocks() as $block) {
                if ($block instanceof ImageBlock) {
                    return new Image($block->getView());
                }
            }
        }

        return null;
    }

    public function getAllImages(string $field): ImmVector<Image>
    {
        $fragments = $this->getAll($field);
        $images = new Vector<Image>();
        foreach ($fragments as $fragment) {
            if (isset($fragment) && $fragment instanceof Image) {
                $images->add($fragment);
            } elseif (isset($fragment) && $fragment instanceof StructuredText) {
                foreach ($fragment->getBlocks() as $block) {
                    if ($block instanceof ImageBlock) {
                        $images->add(new Image($block->getView()));
                    }
                }
            }
        }

        return $images->toImmVector();
    }

    public function getImageView(string $field, ?string $view = null): ?ImageView
    {
        $fragment = $this->get($field);
        if (isset($fragment) && $fragment instanceof Image) {
            return $fragment->getView($view);
        } elseif (isset($fragment) && $fragment instanceof StructuredText && $view == 'main') {
            $maybeImage = $this->getImage($field);
            if (isset($maybeImage)) {
                return $maybeImage->getMain();
            }
        }

        return null;
    }

    public function getAllImageViews(string $field, $view): ImmVector<ImageView>
    {
        $imageViews = new Vector();
        foreach ($this->getAllImages($field) as $image) {
            $imageView = $image->getView($view);
            if (isset($imageView)) {
                $imageViews->add($imageView);
            }
        };

        return $imageViews->toImmVector();
    }

    public function getStructuredText(string $field): ?StructuredText
    {
        $fragment = $this->get($field);
        if (isset($fragment) && $fragment instanceof StructuredText) {
            return $fragment;
        }

        return null;
    }

    public function getGroup(string $field): ?Group
    {
        $fragment = $this->get($field);
        if (isset($fragment) && $fragment instanceof Group) {
            return $fragment;
        }

        return null;
    }

    public function asHtml(?LinkResolver $linkResolver = null): string
    {
        $html = null;
        foreach ($this->fragments as $field => $v) {
            $html = $html . '<section data-field="' . $field . '">' . $this->getHtml($field, $linkResolver) . '</section>';
        };

        return $html;
    }
}