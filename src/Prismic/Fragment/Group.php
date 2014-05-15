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

use Prismic\Document;
use Prismic\LinkResolver;
use Prismic\Fragment\FragmentInterface;

class Group implements FragmentInterface
{
    private ImmVector<Pair<string, FragmentInterface>> $docs;

    public function __construct(ImmVector<Pair<string, FragmentInterface>> $docs)
    {
        $this->docs = $docs;
    }

    public function asHtml(?LinkResolver $linkResolver = null): string
    {
        $string = "";
        foreach ($this->docs as $subfragments) {
            foreach ($subfragments as $name => $subfragment) {
                if($subfragment instanceof FragmentInterface) {
                    $html = $subfragment->asHtml($linkResolver);
                    $string .= "<section data-field=\"$name\">" . $html . "</section>";
                }
            }
        }
        return $string;
    }

    public function getDocs(): ImmVector<Pair<string, FragmentInterface>>
    {
        return $this->docs;
    }

    public static function parseSubfragments(ImmMap<string, mixed> $json): ImmMap<string, FragmentInterface>
    {
        $subfragments = Map {};
        foreach ($json as $name => $value) {
            $value = \Prismic\Tools::requireImmMap($value);
            $f = Document::parseFragment($value);
            if (!is_null($f)) {
                $subfragments->add(Pair { $name, $f });
            }
        }

        return $subfragments->toImmMap();
    }

    public static function parse(ImmMap<string, mixed> $json): Group
    {
        $results = Vector {};
        foreach ($json as $subfragments) {
            $subfragments = \Prismic\Tools::requireImmMap($subfragments);
            $fs = Group::parseSubfragments($subfragments);
            $results->add($fs);
        }

        return new Group($results->toImmVector());
    }
}
