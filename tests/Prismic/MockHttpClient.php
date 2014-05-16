<?hh

namespace Prismic\Test;

use Prismic\Http\HttpClientInterface;

class MockHttpClient implements HttpClientInterface {

    private $client;

    public function __construct($client) {
        $this->client = $client;
    }

    public function get(string $url): ImmMap<string, mixed> {
        $request = $this->client->get($url);
        $json = @json_decode($request->send()->getBody(true), true);
        return new ImmMap($json);
    }
}