<?hh

namespace Prismic\Test;

use Prismic\Document;
use \Prismic\Fragment\ImageView;
use DOMDocument;
use DOMXpath;

class ImageViewTest extends \PHPUnit_Framework_TestCase
{

    protected $inputs;

    protected function setUp()
    {
        $search = json_decode(file_get_contents(__DIR__.'/../fixtures/search.json'), true);
        $document = Document::parse(new ImmMap($search[0]));
        $gallery = $document->getImage('product.gallery');
        if($gallery) {
            $views = $gallery->getViews()->values()->toVector();
            $views->add($gallery->getMain());
            $this->inputs = Vector {};
            foreach ($views as $view) {
                $dom = new DOMDocument();
                $parsed = $dom->loadHTML($view->asHtml());
                if($this->inputs) {
                    $this->inputs->add(tuple($view, $parsed, $dom));
                }
            }
        }
    }

    public function testStartsWithImg()
    {
        foreach ($this->inputs as $input) {
            $view = $input[0];
            $this->assertRegExp('/^<img\b/', $view->asHtml());
        }
    }

    public function testParsable()
    {
        foreach ($this->inputs as $input) {
            $parsed = $input[1];
            $this->assertTrue($parsed);
        }
    }

    public function testExactlyOneImage()
    {
        $imgs = array();
        foreach ($this->inputs as $input) {
            $dom = $input[2];
            $xpath = new DOMXpath($dom);
            $results = $xpath->query('//img');
            $this->assertEquals($results->length, 1);
            $imgs[] = $results->item(0);
        }
        return $imgs;
    }

    /**
     * @depends testExactlyOneImage
     */
    public function testImageHasNoSiblings(array $imgs)
    {
        foreach ($imgs as $img) {
            $this->assertNull($img->nextSibling);
            $this->assertNull($img->previousSibling);
        }
    }

    /**
     * @depends testExactlyOneImage
     */
    public function testAttributes(array $imgs)
    {
        foreach ($imgs as $index => $img) {
            $input = $this->inputs->at($index);
            $view = $input[0];
            $this->assertTrue($img->hasAttribute('src'));
            $this->assertEquals($img->getAttribute('src'), $view->getUrl());
            $this->assertTrue($img->hasAttribute('width'));
            $this->assertEquals($img->getAttribute('width'), $view->getWidth());
            $this->assertTrue($img->hasAttribute('height'));
            $this->assertEquals($img->getAttribute('height'), $view->getHeight());
            $this->assertTrue($img->hasAttribute('alt'));
            $this->assertEquals($img->getAttribute('alt'), $view->getAlt());
        }
    }
}
