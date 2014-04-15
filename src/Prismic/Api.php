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

use Guzzle\Http\Client;
use Prismic\FieldForm;

class Api
{
    protected $accessToken;
    protected $data;

    /**
     * @param ApiData $data
     * @param string $accessToken
     */
    private function __construct(ApiData $data, ?string $accessToken)
    {
        $this->data        = $data;
        $this->accessToken = $accessToken;
    }

    /**
     * returns all repositories references
     *
     * @return array
     */
    public function refs(): ImmMap<string, Ref>
    {
        $refs = $this->data->getRefs();
        $groupBy = array();
        foreach ($refs as $ref) {
            if (isset($refs[$ref->getLabel()])) {
                $arr = $refs[$ref->getLabel()];
                array_push($arr, $ref);
                $groupBy[$ref->getLabel()] = $arr;
            } else {
                $groupBy[$ref->getLabel()] = array($ref);
            }
        }

        $results = ImmMap<string, Ref>();
        foreach ($groupBy as $label => $values) {
            $results[$label] = $values[0];
        }

        return $results;
    }

    public function bookmarks(): ImmMap<string, string>
    {
        return $this->data->getBookmarks();
    }

    public function bookmark($name): ?string
    {
        return $this->bookmarks()->get($name);
    }

    /**
     * returns the master reference repository
     *
     * @return string
     */
    public function master(): Ref
    {
        return $this->data->getRefs()->filter($ref ==> $ref->isMasterRef())->at(0);
    }

    /**
     * returns all forms availables
     *
     * @return mixed
     */
    public function forms(): ImmMap<string, SearchForm>
    {
        return $this->data->getForms()->map($form ==> new SearchForm($this, $form, $form->defaultData()));
    }

    /**
     * @return string
     */
    public function oauthInitiateEndpoint(): string
    {
        return $this->data->getOauthInitiate();
    }

    /**
     * @return string
     */
    public function oauthTokenEndpoint(): string
    {
        return $this->data->getOauthToken();
    }

    public function getData(): ApiData
    {
        return $this->data;
    }

    /**
     * This method is static to respect others API
     *
     * @param string $action
     * @param string $accessToken
     * @param Client $client
     * @return Api
     */
    public static function get(string $action, string $accessToken=null, Client $client=null): Api
    {
        $url = $action . ($accessToken ? '?access_token=' . $accessToken : '');
        $client = isset($client) ? $client : self::defaultClient();
        $request = $client->get($url);
        $response = $request->send();

        $response = @json_decode($response->getBody(true));

        if (!$response) {
            throw new \RuntimeException('Unable to decode the json response');
        }

        $apiData = new ApiData(
            (new ImmVector($response->refs))->map($ref ==> Ref::parse($ref)),
            new ImmMap((array)$response->bookmarks),
            new ImmMap((array)$response->types),
            new ImmVector($response->tags),
            (new ImmMap((array)$response->forms))->map($data ==> Form::parse($data)),
            $response->oauth_initiate,
            $response->oauth_token
        );

        return new Api($apiData, $accessToken);
    }

    public static function defaultClient(): Client
    {
        return new Client('', array(
            Client::CURL_OPTIONS => array(
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 60,
                CURLOPT_USERAGENT      => 'prismic-php-0.1',
                CURLOPT_HTTPHEADER     => array('Accept: application/json')
            )
        ));
    }
}
