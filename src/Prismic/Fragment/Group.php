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
            foreach ($subfragments as $subfragment_name => $subfragment) {
                $string .= "<section data-field=\"$subfragment_name\">" .
                           $subfragment->asHtml($linkResolver) .
                           "</section>";
            }
        }
        return $string;
    }

    public function asText(): string
    {
        $string = "";
        foreach ($this->array as $subfragments) {
            foreach ($subfragments as $subfragment_name => $subfragment) {
                $string .= $subfragment->asText();
            }
        }
        return $string;
    }

    public function getDocs(): ImmVector<Pair<string, FragmentInterface>>
    {
        return $this->docs;
    }

    public static function parseSubfragments($json): ImmMap<string, FragmentInterface>
    {
        $subfragments = new Map();
        foreach ($json as $name => $value) {
            $f = Document::parseFragment($value);
            if (isset($f)) {
                $subfragments->add(Pair { $name, $f });
            }
        }

        return $subfragments->toImmMap();
    }

    public static function parse($json): Group
    {
        $results = new Vector();
        foreach ($json as $subfragments) {
            $fs = Group::parseSubfragments($subfragments);
            $results->add($fs);
        }

        return new Group($results->toImmVector());
    }
}
