<?php

namespace Tests\ShopBundle\Smoke;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Router\CurrentDomainRouter;
use Shopsys\FrameworkBundle\Component\Router\DomainRouterFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tests\ShopBundle\Test\DatabaseTestCase;

class LocalizationListenerTest extends DatabaseTestCase
{
    public function testProductDetailOnFirstDomainHasEnglishLocale()
    {
        $router = $this->getServiceByType(CurrentDomainRouter::class);
        /* @var $router \Shopsys\FrameworkBundle\Component\Router\CurrentDomainRouter */
        $productUrl = $router->generate('front_product_detail', ['id' => 3], UrlGeneratorInterface::ABSOLUTE_URL);

        $crawler = $this->getClient()->request('GET', $productUrl);

        $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Catalogue number")')->count()
        );
    }

    /**
     * @group multidomain
     */
    public function testProductDetailOnSecondDomainHasCzechLocale()
    {
        $domain = $this->getServiceByType(Domain::class);
        /* @var $domain \Shopsys\FrameworkBundle\Component\Domain\Domain */

        $domain->switchDomainById(2);

        $router = $this->getServiceByType(DomainRouterFactory::class)->getRouter(2);
        /* @var $router \Symfony\Component\Routing\RouterInterface */
        $productUrl = $router->generate('front_product_detail', ['id' => 3], UrlGeneratorInterface::ABSOLUTE_URL);
        $crawler = $this->getClient()->request('GET', $productUrl);

        $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Katalogové číslo")')->count()
        );
    }
}
