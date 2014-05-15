<?hh // strict

/*
 * This file is part of the Prismic hack SDK
 *
 * Copyright 2013 Zengularity (http://www.zengularity.com).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prismic;

use Prismic\Fragment\Color;
use Prismic\Fragment\Date;
use Prismic\Fragment\Embed;
use Prismic\Fragment\Image;
use Prismic\Fragment\Number;
use Prismic\Fragment\ImageView;
use Prismic\Fragment\FragmentInterface;
use Prismic\Fragment\Link\DocumentLink;
use Prismic\Fragment\Link\MediaLink;
use Prismic\Fragment\Link\WebLink;
use Prismic\Fragment\StructuredText;
use Prismic\Fragment\Text;
use Prismic\Fragment\Group;
use Prismic\Fragment\Block\ImageBlock;
use Prismic\Fragment\Block\TextInterface;

class Document
{
    private string $id;
    private string $type;
    private string $href;
    private ImmVector<string> $tags;
    private ImmVector<string> $slugs;
    private ImmMap<string, FragmentInterface> $fragments;

    /**
     * @param string    $id
     * @param string    $type
     * @param string    $href
     * @param ImmVector $tags
     * @param ImmVector $slugs
     * @param ImmMap    $fragments
     */
    public function __construct(
        string $id,
        string $type,
        string $href,
        ImmVector<string> $tags,
        ImmVector<string> $slugs,
        ImmMap<string, FragmentInterface> $fragments = ImmMap {}
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->href = $href;
        $this->tags = $tags;
        $this->slugs = $slugs;
        $this->fragments = $fragments;
    }

    public function getSlug(): string
    {
        $maybeSlug = $this->slugs->get(0);
        return !is_null($maybeSlug) ? $maybeSlug : '-';
    }

    public function containsSlug(string $slug): bool
    {
        return $this->slugs->toImmSet()->contains($slug);
    }

    public static function parseFragment(ImmMap<string, mixed> $json): ?FragmentInterface
    {
        if (!is_null($json->get('type'))) {

            $type = $json->at('type');
            $value = $json->at('value');

            if ($type === "Image") {
                $value = Tools::requireImmMap($value);
                $views = Tools::requireImmMap($value->at('views'))->mapWithKey(($key, $json) ==> {
                    return ImageView::parse($json);
                });
                $main = Tools::requireImmMap($value->at('main'));
                $mainView = ImageView::parse($main);
                return new Image($mainView, $views);
            }

            if ($type === "Color") {
                return new Color((string)$value);
            }

            if ($type === "Number") {
                return new Number((float)$value);
            }

            if ($type === "Date") {
                return new Date((string)$value);
            }

            if ($type === "Text") {
                return new Text((string)$value);
            }

            if ($type === "Select") {
                return new Text((string)$value);
            }

            if ($type === "Embed") {
                $value = Tools::requireImmMap($value);
                return Embed::parse($value);
            }

            if ($type === "Link.web") {
                $value = Tools::requireImmMap($value);
                return WebLink::parse($value);
            }

            if ($type === "Link.document") {
                $value = Tools::requireImmMap($value);
                return DocumentLink::parse($value);
            }

            if ($type === "Link.file") {
                $value = Tools::requireImmMap($value);
                return MediaLink::parse($value);
            }

            if ($type === "StructuredText") {
                $value = Tools::requireImmMap($value);
                return StructuredText::parse($value);
            }

            if ($type === "Group") {
                $value = Tools::requireImmMap($value);
                return Group::parse($value);
            }

            return null;
        }
        return null;
    }

    public static function parse(ImmMap<string, mixed> $json): Document
    {
        $fragments = Map {};
        $data = Tools::requireImmMap($json->at('data'));
        foreach ($data as $type => $fields) {
            $fields = Tools::requireImmMap($fields);
            foreach ($fields as $key => $value) {
                if (is_array($value)) {
                    for ($i = 0; $i < count($value); $i++) {
                        $f = self::parseFragment($value[$i]);
                        if (!is_null($f)) {
                            $fragments->set($type . '.' . $key . '[' . $i . ']', $f);
                        }
                    }
                } else {
                    $f = self::parseFragment($value);
                    if (!is_null($f)) {
                        $fragments->set($type . '.' . $key, $f);
                    }
                }
            }
        }

        $tags = Tools::requireImmVector($json->at('tags'));
        $slugs = Tools::requireImmVector($json->at('slugs'));

        return new Document(
            (string)$json->at('id'),
            (string)$json->at('type'),
            (string)$json->at('href'),
            $tags,
            $slugs,
            $fragments->toImmMap()
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function getTags(): ImmVector<string>
    {
        return $this->tags;
    }

    public function getSlugs(): ImmVector<string>
    {
        return $this->slugs;
    }

    public function has(string $field): bool
    {
        return $this->fragments->containsKey($field);
    }

    public function get(string $field): ?FragmentInterface {
        $maybeFragment = $this->fragments->get($field);
        if($maybeFragment) {
            return $maybeFragment;
        } else {
            return $this->getAll($field)->get(0);
        }
    }

    public function getAll(string $field): ImmVector<FragmentInterface> {
        $indexedKey = '/^([^\[]+)(\[\d+\])?$/';
        return $this->fragments
                    ->filterWithKey(($key, $value) ==> {
                        $groups = null;
                        if (preg_match($indexedKey, $key, $groups) == 1) {
                            return !is_null($groups) && $groups[1] == $field;
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
        if ($fragment && $fragment instanceof StructuredText) {
            $text = "";
            foreach ($fragment->getBlocks() as $block) {
                if ($block instanceof TextInterface) {
                    $text = $text . $block->getText();
                    $text = $text . "\n";
                }
            }
            return trim($text);
        } elseif ($fragment && $fragment instanceof Number) {
            return (string)$fragment->getValue();
        } elseif ($fragment && $fragment instanceof Color) {
            return $fragment->getHexValue();
        } elseif ($fragment && $fragment instanceof Text) {
            return $fragment->getValue();
        } elseif ($fragment && $fragment instanceof Date) {
            return $fragment->getValue();
        }

        return "";
    }

    public function getNumber(string $field): ?Number
    {
        $fragment = $this->get($field);
        if ($fragment && $fragment instanceof Number) {
            return $fragment;
        }

        return null;
    }

    public function getNumberAs(string $field, string $pattern): ?string
    {
        $fragment = $this->get($field);
        if ($fragment && $fragment instanceof Number) {
            if ($pattern && $fragment) {
                return $fragment->asText($pattern);
            }
        }

        return null;
    }

    public function getBoolean(string $field): ?bool
    {
        $fragment = $this->get($field);
        if ($fragment && $fragment instanceof Text) {
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
        if ($fragment && $fragment instanceof Date) {
            return $fragment;
        }

        return null;
    }

    public function getDateAs(string $field, ?string $pattern = null): ?string
    {
        $fragment = $this->get($field);
        if ($fragment && $fragment instanceof Date) {
            if (!is_null($pattern)) {
                return $fragment->formatted($pattern);
            }
        }

        return null;
    }

    public function getHtml(string $field, ?LinkResolver $linkResolver = null): string
    {
        $fragment = $this->get($field);
        if ($fragment && method_exists($fragment, 'asHtml')) {
            return $fragment->asHtml($linkResolver);
        }

        return "";
    }

    public function getImage(string $field): ?Image
    {
        $fragment = $this->get($field);
        if ($fragment && $fragment instanceof Image) {
            return $fragment;
        } elseif ($fragment && $fragment instanceof StructuredText) {
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
        $images = Vector {};
        foreach ($fragments as $fragment) {
            if ($fragment && $fragment instanceof Image) {
                $images->add($fragment);
            } elseif ($fragment && $fragment instanceof StructuredText) {
                foreach ($fragment->getBlocks() as $block) {
                    if ($block instanceof ImageBlock) {
                        $images->add(new Image($block->getView()));
                    }
                }
            }
        }

        return $images->toImmVector();
    }

    public function getImageView(string $field, string $view): ?ImageView
    {
        $fragment = $this->get($field);
        if ($fragment && $fragment instanceof Image) {
            return $fragment->getView($view);
        } elseif ($fragment && $fragment instanceof StructuredText && $view == 'main') {
            $maybeImage = $this->getImage($field);
            if ($maybeImage) {
                return $maybeImage->getMain();
            }
        }

        return null;
    }

    public function getAllImageViews(string $field, string $view): ImmVector<ImageView>
    {
        $imageViews = Vector {};
        foreach ($this->getAllImages($field) as $image) {
            $imageView = $image->getView($view);
            if ($imageView) {
                $imageViews->add($imageView);
            }
        };

        return $imageViews->toImmVector();
    }

    public function getStructuredText(string $field): ?StructuredText
    {
        $fragment = $this->get($field);
        if ($fragment && $fragment instanceof StructuredText) {
            return $fragment;
        }

        return null;
    }

    public function getGroup(string $field): ?Group
    {
        $fragment = $this->get($field);
        if ($fragment && $fragment instanceof Group) {
            return $fragment;
        }

        return null;
    }

    public function asHtml(?LinkResolver $linkResolver = null): string
    {
        $html = "";
        foreach ($this->fragments as $field => $v) {
            $html = $html . '<section data-field="' . $field . '">' . $this->getHtml($field, $linkResolver) . '</section>';
        };

        return $html;
    }
}
