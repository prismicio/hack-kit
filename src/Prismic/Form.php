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

class Form
{
    private $maybeName;
    private $method;
    private $maybeRel;
    private $enctype;
    private $action;
    private $fields;

    /**
     * @param string    $maybeName
     * @param string    $method
     * @param string    $maybeRel
     * @param string    $enctype
     * @param ImmMap    $action
     * @param FieldForm $fields
     */
    public function __construct(
        ?string $maybeName,
        string $method,
        ?string $maybeRel,
        string $enctype,
        string $action,
        ImmMap<string, FieldForm> $fields
    ) {
        $this->maybeName = $maybeName;
        $this->method = $method;
        $this->maybeRel = $maybeRel;
        $this->enctype = $enctype;
        $this->action = $action;
        $this->fields = $fields;
    }

    public function defaultData(): ImmMap<string, ImmVector<string>>
    {
        return $this->getFields()
                    ->map($f ==> $f->getDefaultValue())
                    ->filter($v ==> isset($v))
                    ->map($value ==> new ImmVector(array($value)));
    }

    public function getName(): ?string
    {
        return $this->maybeName;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getRel(): ?string
    {
        return $this->maybeRel;
    }

    public function getEnctype(): string

    {
        return $this->enctype;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getFields(): ImmMap<String, FieldForm>
    {
        return $this->fields;
    }

    public static function parse($json): Form
    {
        return new Form(
            isset($json->{"name"}) ? $json->{"name"} : null,
            $json->method,
            isset($json->{"rel"}) ? $json->{"rel"} : null,
            $json->enctype,
            $json->action,
            (new ImmMap((array)$json->fields))->map($data ==> FieldForm::parse($data))
        );
    }
}
