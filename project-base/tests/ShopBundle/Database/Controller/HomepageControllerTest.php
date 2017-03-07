<?php

namespace Tests\ShopBundle\Database\Controller;

use Tests\ShopBundle\Test\FunctionalTestCase;

class HomepageControllerTest extends FunctionalTestCase
{
    public function testHomepageHttpStatus200()
    {
        $client = $this->getClient();

        $client->request('GET', '/');
        $code = $client->getResponse()->getStatusCode();

        $this->assertSame(200, $code);
    }

    public function testHomepageHasBodyEnd()
    {
        $client = $this->getClient();

        $client->request('GET', '/');
        $content = $client->getResponse()->getContent();

        $this->assertRegExp('/<\/body>/ui', $content);
    }
}
