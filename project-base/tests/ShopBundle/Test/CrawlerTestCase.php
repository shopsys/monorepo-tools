<?php

namespace Tests\ShopBundle\Test;

use Symfony\Bundle\FrameworkBundle\Client;
use Tests\ShopBundle\Crawler\ResponseTest\UrlsProvider;

abstract class CrawlerTestCase extends FunctionalTestCase
{
    /**
     * @return \Tests\ShopBundle\Crawler\ResponseTest\UrlsProvider
     */
    protected function createUrlsProvider()
    {
        $container = $this->getContainer();

        return new UrlsProvider(
            $container->get('shopsys.shop.component.data_fixture.persistent_reference_facade'),
            $container->get('shopsys.shop.router.current_domain_router'),
            $container->get('security.csrf.token_manager'),
            $container->get('shopsys.shop.router.security.route_csrf_protector'),
            $container->get('shopsys.shop.component.domain')
        );
    }

    /**
     * @param \Symfony\Bundle\FrameworkBundle\Client $client
     * @param string $url
     */
    protected function makeRequestInTransaction(Client $client, $url)
    {
        $clientEntityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        /* @var $clientEntityManager \Doctrine\ORM\EntityManager */

        $clientEntityManager->beginTransaction();

        $client->request('GET', $url);

        $clientEntityManager->rollback();
    }
}
