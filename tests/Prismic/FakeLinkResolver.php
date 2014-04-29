<?hh

namespace Prismic\Test;

use Prismic\LinkResolver;
use Prismic\Fragment\Link\DocumentLink;

class FakeLinkResolver extends LinkResolver
{
    public function resolve(DocumentLink $link): string
    {
        return "http://host/doc/".$link->getId();
    }
}
