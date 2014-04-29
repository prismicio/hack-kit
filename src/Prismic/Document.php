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

    private $id;
    private $type;
    private $href;
    private $tags;
    private $slugs;
    private $fragments;

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
        ImmMap<string, FragmentInterface> $fragments
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

    public function getSlug(): ?string
    {
        if ($this->slugs->count() > 0) {
            return $this->slugs->get(0);
        }

        return null;
    }

    public function containsSlug(string $slug): bool
    {
        return $this->slugs->toImmSet()->contains($slug);
    }

    public static function parseFragment(\stdClass $json): ?FragmentInterface
    {
        if (is_object($json) && property_exists($json, "type")) {
            if ($json->type === "Image") {
                $data = $json->value;
                $views = array();
                foreach ($json->value->views as $key => $jsonView) {
                    $views[$key] = ImageView::parse($jsonView);
                }
                $mainView = ImageView::parse($data->main, $views);

                return new Image($mainView, $views);
            }

            if ($json->type === "Color") {
                return new Color($json->value);
            }

            if ($json->type === "Number") {
                return new Number($json->value);
            }

            if ($json->type === "Date") {
                return new Date($json->value);
            }

            if ($json->type === "Text") {
                return new Text($json->value);
            }

            if ($json->type === "Select") {
                return new Text($json->value);
            }

            if ($json->type === "Embed") {
                return Embed::parse($json->value);
            }

            if ($json->type === "Link.web") {
                return WebLink::parse($json->value);
            }

            if ($json->type === "Link.document") {
                return DocumentLink::parse($json->value);
            }

            if ($json->type === "Link.file") {
                return MediaLink::parse($json->value);
            }

            if ($json->type === "StructuredText") {
                return StructuredText::parse($json->value);
            }

            if ($json->type === "Group") {
                return Group::parse($json->value);
            }

            return null;
        }
    }

    public static function parse(\stdClass $json): Document
    {
        $fragments = new Map<string, FragmentInterface>();
        foreach ($json->data as $type => $fields) {
            foreach ($fields as $key => $value) {
                if (is_array($value)) {
                    for ($i = 0; $i < count($value); $i++) {
                        $f = self::parseFragment($value[$i]);
                        if (isset($f)) {
                            $fragments->set($type . '.' . $key . '[' . $i . ']', $f);
                        }
                    }
                } else {
                    $f = self::parseFragment($value);
                    if (isset($f)) {
                        $fragments->set($type . '.' . $key, $f);
                    }
                }
            }
        }

        return new Document(
            $json->id,
            $json->type,
            $json->href,
            new ImmVector($json->tags),
            new ImmVector($json->slugs),
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
