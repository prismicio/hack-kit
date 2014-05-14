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

class FieldForm
{

    private string $type;
    private bool $multiple;
    private ?string $defaultValue;

    /**
     * @param string  $type
     * @param bool $multiple
     * @param string  $defaultValue
     */
    public function __construct(string $type, bool $mutiple, ?string $defaultValue)
    {
        $this->type = $type;
        $this->multiple = $mutiple;
        $this->defaultValue = $defaultValue;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    public static function parse($json): FieldForm
    {
        return new FieldForm(
            $json->type,
            isset($json->multiple) ? $json->multiple : false,
            $json->default
        );
    }
}
