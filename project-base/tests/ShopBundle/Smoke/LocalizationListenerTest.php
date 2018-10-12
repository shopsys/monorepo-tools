<?php

namespace Tests\ShopBundle\Smoke;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Router\CurrentDomainRouter;
use Shopsys\FrameworkBundle\Component\Router\DomainRouterFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class LocalizationListenerTest extends TransactionFunctionalTestCase
{
    public function testProductDetailOnFirstDomainHasEnglishLocale()
    {
        /** @var \Shopsys\FrameworkBundle\Component\Router\CurrentDomainRouter $router */
        $router = $this->getContainer()->get(CurrentDomainRouter::class);
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
        /** @var \Shopsys\FrameworkBundle\Component\Domain\Domain $domain */
        $domain = $this->getContainer()->get(Domain::class);

        $domain->switchDomainById(2);

        /** @var \Symfony\Component\Routing\RouterInterface $router */
        $router = $this->getContainer()->get(DomainRouterFactory::class)->getRouter(2);
        $productUrl = $router->generate('front_product_detail', ['id' => 3], UrlGeneratorInterface::ABSOLUTE_URL);
        $crawler = $this->getClient()->request('GET', $productUrl);

        $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Katalogové číslo")')->count()
        );
    }
}
