<?hh

namespace Prismic\Test;

use Prismic\API;
use Prismic\Document;
use Prismic\Fragment\Link\DocumentLink;

class LinkResolverTest extends \PHPUnit_Framework_TestCase
{
    private function initApi($client) {
        $response = $this->getMockBuilder('Guzzle\Http\Message\Response')
                         ->disableOriginalConstructor()
                         ->getMock();
        $response->expects($this->once())->method('getBody')->will($this->returnValue(file_get_contents(__DIR__.'/../fixtures/data.json')));
        $request = $this->getMock('Guzzle\Http\Message\RequestInterface');
        $request->expects($this->once())->method('send')->will($this->returnValue($response));
        $client->expects($this->once())->method('get')->will($this->returnValue($request));
        $this->api = Api::get('don\'t care about this value', null, new MockHttpClient($client));
    }

    protected function setUp()
    {
        $this->tags = new ImmVector(array('macaron'));
        $this->type = 'product';
        $this->linkResolver = new FakeLinkResolver();
        $this->id = 'Ue0EDd_mqb8Dhk3j';
        $this->slugs = new ImmVector(array('ABCD'));
        $this->slug = $this->slugs->at(0);
        $this->link = new DocumentLink($this->id, $this->type, $this->tags, $this->slug, false);
        $href = "http://myrepo.prismic.io/Ue0EDd_mqb8Dhk3j";
        $this->document = new Document($this->id, $this->type, $href, $this->tags, $this->slugs);
        $client = $this->getMock('Guzzle\Http\Client');
        $this->initApi($client);
    }

    public function testResolveDocumentLink()
    {
        $content = '<a href="http://host/doc/Ue0EDd_mqb8Dhk3j">ABCD</a>';
        $this->assertEquals($content, $this->link->asHtml($this->linkResolver));
    }

    public function testResolve()
    {
        $content = 'http://host/doc/Ue0EDd_mqb8Dhk3j';
        $this->assertEquals($content, $this->linkResolver->resolve($this->link));
    }

    public function testCall()
    {
        $content = 'http://host/doc/Ue0EDd_mqb8Dhk3j';
        $linkResolver = $this->linkResolver;
        $this->assertEquals($content, $linkResolver->resolve($this->link));
    }

    public function testResolveDocument()
    {
        $content = "http://host/doc/Ue0EDd_mqb8Dhk3j";
        $this->assertEquals($content, $this->linkResolver->resolveDocument($this->document));
    }

    public function testResolveLink()
    {
        $content = "http://host/doc/Ue0EDd_mqb8Dhk3j";
        $this->assertEquals($content, $this->linkResolver->resolveLink($this->link));
    }

    public function testIsBookmarkNotFound()
    {
        $bookmark = "macaron_d_or";
        $this->assertFalse($this->linkResolver->isBookmark($this->api, $this->link, $bookmark));
    }

    public function testIsBookmarkFound()
    {
        $bookmark = "about";
        $this->assertTrue($this->linkResolver->isBookmark($this->api, $this->link, $bookmark));
    }

    public function testIsBookmarkDocumentNotFound()
    {
        $bookmark = "macaron_d_or";
        $this->assertFalse($this->linkResolver->isBookmarkDocument($this->api, $this->document, $bookmark));
    }

    public function testIsBookmarkDocument()
    {
        $bookmark = "about";
        $this->assertTrue($this->linkResolver->isBookmarkDocument($this->api, $this->document, $bookmark));
    }

}
