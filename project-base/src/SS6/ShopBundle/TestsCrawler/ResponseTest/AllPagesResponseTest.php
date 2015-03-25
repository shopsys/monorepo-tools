<?php

namespace SS6\ShopBundle\TestsCrawler\ResponseTest;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\TestsCrawler\ResponseTest\UrlsProvider;

class AllPagesResponseTest extends DatabaseTestCase {

	public function adminTestableUrlsProvider() {
		$domain = $this->getContainer()->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */
		$router = $this->getContainer()->get('router');
		/* @var $router \Symfony\Component\Routing\RouterInterface */
		$persistentReferenceService = $this->getContainer()->get('ss6.shop.data_fixture.persistent_reference_service');
		/* @var $router \SS6\ShopBundle\Component\DataFixture\PersistentReferenceService */

		// DataProvider is called before setUp() - domain is not set
		$domain->switchDomainById(1);

		$urlsProvider = new UrlsProvider($persistentReferenceService, $router);

		return $urlsProvider->getAdminTestableUrlsProviderData();
	}

	/**
	 * @param string $testedRouteName Used for easier debugging
	 * @param string $url
	 * @param int $expectedStatusCode
	 * @dataProvider adminTestableUrlsProvider
	 */
	public function testAdminPages($testedRouteName, $url, $expectedStatusCode) {
		$this->getClient(false, 'admin', 'admin123')->request('GET', $url);
		$this->assertSame($expectedStatusCode, $this->getClient()->getResponse()->getStatusCode());
	}

	public function frontTestableUrlsProvider() {
		$domain = $this->getContainer()->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */
		$router = $this->getContainer()->get('router');
		/* @var $router \Symfony\Component\Routing\RouterInterface */
		$persistentReferenceService = $this->getContainer()->get('ss6.shop.data_fixture.persistent_reference_service');
		/* @var $router \SS6\ShopBundle\Component\DataFixture\PersistentReferenceService */

		// DataProvider is called before setUp() - domain is not set
		$domain->switchDomainById(1);
		$urlsProvider = new UrlsProvider($persistentReferenceService, $router);

		return $urlsProvider->getFrontTestableUrlsProviderData();
	}

	/**
	 * @param string $testedRouteName Used for easier debugging
	 * @param string $url
	 * @param int $expectedStatusCode
	 * @param bool $asLogged
	 * @dataProvider frontTestableUrlsProvider
	 */
	public function testFrontPages($testedRouteName, $url, $expectedStatusCode, $asLogged) {
		if ($asLogged) {
			$this->getClient(false, 'no-reply@netdevelo.cz', 'user123')->request('GET', $url);
		} else {
			$this->getClient()->request('GET', $url);
		}
		$this->assertSame($expectedStatusCode, $this->getClient()->getResponse()->getStatusCode());
	}

}
