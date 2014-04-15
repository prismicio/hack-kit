<?hh

/*
 * This file is part of the Prismic PHP SDK
 *
 * Copyright 2013 Zengularity (http://www.zengularity.com).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prismic;

class FieldForm
{

    private $type;
    private $multiple;
    private $defaultValue;

    /**
     * @param string  $type
     * @param boolean $multiple
     * @param string  $defaultValue
     */
    public function __construct(string $type, boolean $mutiple, ?string $defaultValue)
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

    public function isMultiple(): boolean
    {
        return $this->multiple;
    }

    public static function parse($json): FieldForm
    {
        return new FieldForm(
            $json->type,
            isset($json->{"multiple"}) ? $json->{"multiple"} : false,
            isset($json->{"default"}) ? $json->{"default"} : null
        );
    }
}
