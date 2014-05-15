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

class Tools {

    public static function requireTraversable<T>(mixed $any): Traversable<T> {
        if($any instanceof Traversable) {
            return $any;
        } else {
            throw new \Exception("Unable to get " . (string)$any . " as Traversable");
        }
    }

    public static function requireKeyedTraversable<T>(mixed $any): KeyedTraversable<T, T> {
        if($any instanceof KeyedTraversable) {
            return $any;
        } else {
            throw new \Exception("Unable to get " . (string)$any . " as KeyedTraversable");
        }
    }

    public static function requireImmMap<T>(mixed $any): ImmMap<T, T> {
        $traversable = Tools::requireKeyedTraversable($any);
        return new ImmMap($traversable);
    }

    public static function requireImmVector<T>(mixed $any): ImmVector<T> {
        $traversable = Tools::requireTraversable($any);
        return new ImmVector($traversable);
    }
}