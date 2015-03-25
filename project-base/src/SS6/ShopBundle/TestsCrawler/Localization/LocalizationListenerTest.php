<?php

namespace SS6\ShopBundle\TestsCrawler\Localization;

use SS6\ShopBundle\Component\Router\CurrentDomainRouter;
use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use Symfony\Component\Routing\RouterInterface;

class LocalizationListenerTest extends DatabaseTestCase {

	public function testProductDetailLocaleCs() {
		$router = $this->getContainer()->get(CurrentDomainRouter::class);
		/* @var $router \SS6\ShopBundle\Component\Router\CurrentDomainRouter */
		$productUrl = $router->generate('front_product_detail', ['id' => 3], RouterInterface::RELATIVE_PATH);

		$crawler = $this->getClient()->request('GET', $productUrl);

		$this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());
		$this->assertGreaterThan(
			0,
			$crawler->filter('html:contains("Katalogové číslo")')->count()
		);
	}

	public function testProductDetailLocaleEn() {
		$domain = $this->getContainer()->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */

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
