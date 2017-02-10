<?php

namespace Shopsys\ShopBundle\Tests\Crawler\Localization;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Router\CurrentDomainRouter;
use Shopsys\ShopBundle\Component\Router\DomainRouterFactory;
use Shopsys\ShopBundle\Tests\Test\DatabaseTestCase;
use Symfony\Component\Routing\RouterInterface;

class LocalizationListenerTest extends DatabaseTestCase
{
    public function testProductDetailLocaleCs() {
        $router = $this->getContainer()->get(CurrentDomainRouter::class);
        /* @var $router \Shopsys\ShopBundle\Component\Router\CurrentDomainRouter */
        $productUrl = $router->generate('front_product_detail', ['id' => 3], RouterInterface::RELATIVE_PATH);

        $crawler = $this->getClient()->request('GET', $productUrl);

        $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Katalogové číslo")')->count()
        );
    }

    /**
     * @group multidomain
     */
    public function testProductDetailLocaleEn() {
        $domain = $this->getContainer()->get(Domain::class);
        /* @var $domain \Shopsys\ShopBundle\Component\Domain\Domain */

        $domain->switchDomainById(2);

        $router = $this->getContainer()->get(DomainRouterFactory::class)->getRouter(2);
        /* @var $router \Symfony\Component\Routing\RouterInterface */
        $productUrl = $router->generate('front_product_detail', ['id' => 3], RouterInterface::RELATIVE_PATH);
        $crawler = $this->getClient()->request('GET', $productUrl);

        $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Catalogue number")')->count()
        );
    }
}
