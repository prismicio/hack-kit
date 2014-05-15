<?hh // strict

/*
 * This file is part of the Prismic hack SDK
 *
 * Copyright 2013 Zengularity (http://www.zengularity.com).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prismic\Http;

use Guzzle\Http\Client;

class DefaultHttpClient implements HttpClientInterface {

    private mixed $client;

    public function __construct() {
        // UNSAFE
        $this->client = new Client('', array(
            Client::CURL_OPTIONS => array(
                \CURLOPT_CONNECTTIMEOUT => 10,
                \CURLOPT_RETURNTRANSFER => true,
                \CURLOPT_TIMEOUT        => 60,
                \CURLOPT_USERAGENT      => 'prismic-php-0.1',
                \CURLOPT_HTTPHEADER     => array('Accept: application/json')
            )
        ));
    }

    public function get(string $url): ImmMap<string, mixed> {
        // UNSAFE
        $request = $this->client->get($url);
        $json = @json_decode($request->send()->getBody(true), true);
        return new ImmMap($json);
    }
}