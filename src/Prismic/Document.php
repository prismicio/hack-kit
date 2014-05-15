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

class Document implements WithFragmentsInterface
{
    use WithFragments;

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

    public function fragments(): ImmMap<string, FragmentInterface> {
        return $this->fragments;
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
}
