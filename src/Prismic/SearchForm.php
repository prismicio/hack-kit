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

use Prismic\Document;

class SearchForm
{
    private Api $api;
    private Form $form;
    private ImmMap<string, ImmVector<string>> $data;

    /**
     * @param Api    $client
     * @param Form   $form
     * @param ImmMap $data
     */
    public function __construct(Api $api, Form $form, ImmMap<string, ImmVector<string>> $data)
    {
        $this->api  = $api;
        $this->form = $form;
        $this->data = $data;
    }

    public function set(string $key, string $value): SearchForm
    {
        $maybeField = $this->form->getFields()->get($key);
        if(isset($maybeField)) {
            $values = new Vector(array($value));
            if($maybeField->isMultiple()) {
                $alreadyThere = $this->data->get($key);
                if(isset($alreadyThere)) {
                    $values->addAll($alreadyThere);
                }
            }
            return new SearchForm(
                clone $this->api,
                clone $this->form,
                $this->data->toMap()->add(Pair{$key, $values})->toImmMap()
            );
        } else {
            throw new \RuntimeException("Unknown field " . $field);
        }
    }

    public function setInt(string $key, int $value)
    {
        $maybeField = $this->$form->getFields()->get($key);
        if(isset($maybeField)) {
            if($maybeField->type == 'Integer') {
                $this->set($key, $value->toString());
            } else {
                throw new \RuntimeException("Cannot use a int as value for the field" . $field . "of type"  . $field->type);
            }
        } else {
            throw new \RuntimeException("Unknown field " . $field);
        }
    }

    /**
     * Set the repository reference
     *
     * @param string $ref
     *
     * @return SearchForm
     */
    public function ref(string $ref): SearchForm
    {
        return $this->set("ref", $ref);
    }

    /**
     * Set the repository page size
     *
     * @param  int        $pageSize
     * @return SearchForm
     */
    public function pageSize(int $pageSize): SearchForm
    {
        return $this->setInt("pageSize", $pageSize);
    }

    /**
     * Set the repository page
     *
     * @param  int $page
     * @return SearchForm
     */
    public function page(int $page): SearchForm
    {
        return $this->setInt("page", $page);
    }

    /**
     * Set orderings
     *
     * @param  string $orderings
     * @return SearchForm
     */
    public function orderings(string $orderings): SearchForm
    {
        return $this->setInt("orderings", $orderings);
    }

    /**
     * Create documents from the search results
     *
     * @param $results
     *
     * @return ImmVector
     */
    private static function parseResult(\stdClass $json): ImmVector<Document>
    {
        return (new ImmVector($json->results))->map($doc ==> Document::parse($doc));
    }

    /**
     * Submit the current form to retrieve remote contents
     *
     * @return mixed Array of Document objects
     *
     * @throws \RuntimeException
     */
    public function submit(): ImmVector<Document>
    {
        return self::parseResult($this->submit_raw());
    }

    /**
     * Get the result count for this form
     *
     * This uses a copy of the SearchForm with a page size of 1 (the smallest
     * allowed) since all we care about is one of the returned non-result
     * fields.
     *
     * @return integer Total number of results
     *
     * @throws \RuntimeException
     */
    public function count(): int
    {
        return $this->pageSize(1)->submit_raw()->total_results_size;
    }

    /**
     * Generate a SearchForm instance for the provided query. Please note the ref method need to
     * be call before so the repository is set.
     *
     *    $boundForm = $formSearch->ref('my content repository reference');
     *    $queryForm = $boundForm->query('[[:d = at(document.type, "event")]]');
     *    $results = $queryForm->submit()
     *
     * @param $q
     *
     * @return SearchForm
     */
    public function query(string $q): SearchForm
    {
        $fields = $this->form->getFields();
        $field = $fields['q'];
        if ($field->isMultiple()) {
            return $this->set("q", $q);
        } else {
            // Temporary Hack for backward compatibility
            $maybeDefault = property_exists($field, "defaultValue") ? $field->getDefaultValue() : null;
            $q1 = $maybeDefault ? self::strip($maybeDefault) : "";

            $data = $this->data;
            $data['q'] = '[' . $q1 . self::strip($q) . ']';

            return new SearchForm($this->api, $this->form, $data);
        }
    }

    /**
     * Perform the actual submit call
     *
     * @return the raw (unparsed) response
     */
    private function submit_raw(): \stdClass
    {
        if ($this->form->getMethod() == 'GET' &&
            $this->form->getEnctype() == 'application/x-www-form-urlencoded' &&
            $this->form->getAction()
        ) {
            $url = $this->form->getAction() . '?' . http_build_query($this->data);
            $url = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $url);

            $request = Api::defaultClient()->get($url);
            $response = $request->send();

            $response = @json_decode($response->getBody(true));
            if (!isset($response)) {
                throw new \RuntimeException("Unable to decode json response");
            }

            return $response;
        }

        throw new \RuntimeException("Form type not supported");
    }

    /**
     * Clean the query
     *
     * @param string $str
     *
     * @return string
     */
    private static function strip(string $str): string
    {
        $trimmed = trim($str);
        $drop1 = substr($trimmed, 1, strlen($trimmed));
        $dropR1 = substr($drop1, 0, strlen($drop1) - 1);

        return $dropR1;
    }
}
