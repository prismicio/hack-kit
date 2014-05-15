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

class Form
{
    private ?string $maybeName;
    private string $method;
    private ?string $maybeRel;
    private string $enctype;
    private string $action;
    private ImmMap<string, FieldForm> $fields;

    /**
     * @param string    $maybeName
     * @param string    $method
     * @param string    $maybeRel
     * @param string    $enctype
     * @param ImmMap    $action
     * @param ImmMap    $fields
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
        $defaults = $this->getFields()
                         ->map($f ==> $f->getDefaultValue())
                         ->filter($v ==> !is_null($v));

        return $defaults->map($value ==> new ImmVector(array((string)$value)));
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

    public function getFields(): ImmMap<string, FieldForm>
    {
        return $this->fields;
    }

    public static function parse(ImmMap<string, mixed> $json): Form
    {
        $fields = Tools::requireImmMap($json->at('fields'));
        return new Form(
            (string)$json->at('name'),
            (string)$json->at('method'),
            (string)$json->at('rel'),
            (string)$json->at('enctype'),
            (string)$json->at('action'),
            $fields->map($data ==> FieldForm::parse($data))
        );
    }
}
