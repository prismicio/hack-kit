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
    private bool $isMasterRef;
    private ?string $maybeScheduledAt;

    /**
     * @param string $ref
     * @param string $label
     * @param bool $isMasterRef
     * @param date $maybeScheduledAt
     */
    public function __construct(
        string $ref,
        string $label,
        bool $isMasterRef,
        ?string $maybeScheduledAt
    ) {
        $this->ref = $ref;
        $this->label = $label;
        $this->isMasterRef = $isMasterRef;
        $this->maybeScheduledAt = $maybeScheduledAt;
        isset($this->ref);
    }

    public function getRef(): string
    {
        return $this->ref;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isMasterRef(): bool
    {
        return $this->isMasterRef;
    }

    public function getScheduledAt(): ?string
    {
        return $this->maybeScheduledAt;
    }

    public static function parse(mixed $json): Ref
    {
        return new Ref(
            $json->ref,
            $json->label,
            isset($json->isMasterRef) ? $json->isMasterRef : false,
            $json->scheduledAt
        );
    }
}
