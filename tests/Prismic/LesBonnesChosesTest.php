<?hh

namespace Prismic\Test;

use Prismic\Api;

class LesBonnesChosesTest extends \PHPUnit_Framework_TestCase
{

    private static $testRepository = 'http://lesbonneschoses.prismic.io/api';
    private static $previewToken = 'MC5VbDdXQmtuTTB6Z0hNWHF3.c--_vVbvv73vv73vv73vv71EA--_vS_vv73vv70T77-9Ke-_ve-_vWfvv70ebO-_ve-_ve-_vQN377-9ce-_vRfvv70';

    public function testRetrieveApi()
    {
        $api = Api::get(self::$testRepository);
        $nbRefs = count($api->getData()->getRefs());
        $this->assertEquals($nbRefs, 1);
    }

    public function testSubmitEverythingForm()
    {
        $api = Api::get(self::$testRepository);
        $masterRef = $api->master()->getRef();
        $results = $api->forms()->at("everything")->ref($masterRef)->submit()->getResults();
        $this->assertEquals(count($results), 20);
    }

    public function testSubmitEverythingFormWithPredicate()
    {
        $api = Api::get(self::$testRepository);
        $masterRef = $api->master()->getRef();
        $results = $api->forms()->at("everything")->ref($masterRef)->query('[[:d = at(document.type, "product")]]')->submit()->getResults();
        $this->assertEquals(count($results), 16);
    }

    public function testSubmitProductsForm()
    {
        $api = Api::get(self::$testRepository);
        $masterRef = $api->master()->getRef();
        $results = $api->forms()->at("products")->ref($masterRef)->submit()->getResults();
        $this->assertEquals(count($results), 16);
    }

    public function testSubmitProductsFormWithPredicate()
    {
        $api = Api::get(self::$testRepository);
        $masterRef = $api->master()->getRef();
        $results = $api->forms()->at("products")->ref($masterRef)->query('[[:d = at(my.product.flavour, "Chocolate")]]')->submit()->getResults();
        $this->assertEquals(count($results), 5);
    }

    public function testRetrieveApiWithPrivilege()
    {
        $api = Api::get(self::$testRepository, self::$previewToken);
        $nbRefs = count($api->getData()->getRefs());
        $this->assertEquals($nbRefs, 3);
    }

    public function testSubmitProductsFormInTheFuture()
    {
        $api = Api::get(self::$testRepository, self::$previewToken);
        $refs = $api->refs();
        $future = $refs->at('Announcement of new SF shop');
        $results = $api->forms()->at("products")->ref($future->getRef())->submit()->getResults();
        $this->assertEquals(count($results), 17);
    }
}
