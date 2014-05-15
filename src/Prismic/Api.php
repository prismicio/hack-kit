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

use Prismic\Http\HttpClientInterface;
use Prismic\Http\DefaultHttpClient;
use Prismic\FieldForm;

class Api
{
    protected ?string $accessToken;
    protected ApiData $data;

    /**
     * @param ApiData $data
     * @param string $accessToken
     */
    private function __construct(ApiData $data, ?string $accessToken = null)
    {
        $this->data        = $data;
        $this->accessToken = $accessToken;
    }

    /**
     * returns all repositories references
     *
     * @return ImmMap
     */
    public function refs(): ImmMap<string, Ref>
    {
        $refs = $this->data->getRefs();
        $groupBy = Map {};
        foreach ($refs as $ref) {
            $maybeGrouped = $groupBy->get($ref->getLabel());
            if ($maybeGrouped) {
                $maybeGrouped->add($ref);
                $groupBy->set($ref->getLabel(), $maybeGrouped);
            } else {
                $groupBy->set($ref->getLabel(), new Vector(array($ref)));
            }
        }
        return $groupBy->map($refs ==> $refs->at(0))->toImmMap();
    }

    public function bookmarks(): ImmMap<string, string>
    {
        return $this->data->getBookmarks();
    }

    public function bookmark(string $name): ?string
    {
        return $this->bookmarks()->get($name);
    }

    /**
     * returns the master reference repository
     *
     * @return Ref
     */
    public function master(): Ref
    {
        return $this->data->getRefs()->filter($ref ==> $ref->isMasterRef())->at(0);
    }

    /**
     * returns all forms availables
     *
     * @return ImmMap
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
    public static function get(string $action, ?string $accessToken=null, ?HttpClientInterface $client = null): Api
    {
        $url = $action . (is_null($accessToken) ? '?access_token=' . $accessToken : '');
        $client = $client ? $client : self::defaultClient();
        $response = $client->get($url);

        if (!$response) {
            throw new \RuntimeException('Unable to decode the json response');
        }

        $refs = Tools::requireImmVector($response->at('refs'));
        $bookmarks = Tools::requireImmMap($response->at('bookmarks'));
        $types = Tools::requireImmMap($response->at('types'));
        $tags = Tools::requireImmVector($response->at('tags'));
        $forms = Tools::requireImmMap($response->at('forms'));

        $apiData = new ApiData(
            $refs->map($ref ==> Ref::parse($ref)),
            $bookmarks,
            $types,
            $tags,
            $forms->map($data ==> Form::parse($data)),
            (string)$response->at('oauth_initiate'),
            (string)$response->at('oauth_token')
        );

        return new Api($apiData, $accessToken);
    }

    public static function defaultClient(): HttpClientInterface
    {
        return new DefaultHttpClient();
    }
}
