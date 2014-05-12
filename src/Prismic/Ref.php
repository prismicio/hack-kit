<?hh // strict

/*
 * This file is part of the Prismic PHP SDK
 *
 * Copyright 2013 Zengularity (http://www.zengularity.com).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prismic;

class Ref
{
    private string $ref;
    private string $label;
    private boolean $isMasterRef;
    private ?string $maybeScheduledAt;

    /**
     * @param string $ref
     * @param string $label
     * @param boolean $isMasterRef
     * @param date $maybeScheduledAt
     */
    public function __construct(
        string $ref,
        string $label,
        boolean $isMasterRef,
        ?string $maybeScheduledAt
    ) {
        $this->ref = $ref;
        $this->label = $label;
        $this->isMasterRef = $isMasterRef;
        $this->maybeScheduledAt = $maybeScheduledAt;
    }

    public function getRef(): string
    {
        return $this->ref;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isMasterRef(): boolean
    {
        return $this->isMasterRef;
    }

    public function getScheduledAt(): ?string
    {
        return $this->maybeScheduledAt;
    }

    public static function parse($json): Ref
    {
        return new Ref(
            $json->ref,
            $json->label,
            isset($json->{'isMasterRef'}) ? $json->isMasterRef : false,
            isset($json->{'scheduledAt'}) ? $json->scheduledAt : null    // @todo: convert value into \DateTime ?
        );
    }
}
