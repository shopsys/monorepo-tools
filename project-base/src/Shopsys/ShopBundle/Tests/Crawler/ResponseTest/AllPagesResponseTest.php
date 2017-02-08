<?php

namespace SS6\ShopBundle\Tests\Crawler\ResponseTest;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Tests\Crawler\ResponseTest\UrlsProvider;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

class AllPagesResponseTest extends DatabaseTestCase {

	public function adminTestableUrlsProvider() {
		$domain = $this->getContainer()->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Component\Domain\Domain */
		// DataProvider is called before setUp() - domain is not set
		$domain->switchDomainById(1);

		$urlsProvider = $this->getContainer()->get(UrlsProvider::class);
		/* @var $urlsProvider \SS6\ShopBundle\Tests\Crawler\ResponseTest\UrlsProvider */

		return $urlsProvider->getAdminTestableUrlsProviderData();
	}

	/**
	 * @param string $testedRouteName Used for easier debugging
	 * @param string $url
	 * @param int $expectedStatusCode
	 * @dataProvider adminTestableUrlsProvider
	 */
	public function testAdminPages($testedRouteName, $url, $expectedStatusCode) {
		$urlsProvider = $this->getContainer()->get(UrlsProvider::class);
		/* @var $urlsProvider \SS6\ShopBundle\Tests\Crawler\ResponseTest\UrlsProvider */
		$url = $urlsProvider->replaceCsrfTokensInUrl($url);

		$this->getClient(false, 'superadmin', 'admin123')->request('GET', $url);

		$statusCode = $this->getClient()->getResponse()->getStatusCode();

		$this->assertSame(
			$expectedStatusCode,
			$statusCode,
			sprintf(
				'Failed asserting that status code %d for route "%s" is identical to expected %d',
				$testedRouteName,
				$statusCode,
				$expectedStatusCode
			)
		);
	}

	public function frontTestableUrlsProvider() {
		$domain = $this->getContainer()->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Component\Domain\Domain */
		// DataProvider is called before setUp() - domain is not set
		$domain->switchDomainById(1);

		$urlsProvider = $this->getContainer()->get(UrlsProvider::class);
		/* @var $urlsProvider \SS6\ShopBundle\Tests\Crawler\ResponseTest\UrlsProvider */

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
		$urlsProvider = $this->getContainer()->get(UrlsProvider::class);
		/* @var $urlsProvider \SS6\ShopBundle\Tests\Crawler\ResponseTest\UrlsProvider */
		$url = $urlsProvider->replaceCsrfTokensInUrl($url);

		if ($asLogged) {
			$this->getClient(false, 'no-reply@netdevelo.cz', 'user123')->request('GET', $url);
		} else {
			$this->getClient()->request('GET', $url);
		}

		$statusCode = $this->getClient()->getResponse()->getStatusCode();

		$this->assertSame(
			$expectedStatusCode,
			$statusCode,
			sprintf(
				'Failed asserting that status code %d for route "%s" is identical to expected %d',
				$testedRouteName,
				$statusCode,
				$expectedStatusCode
			)
		);
	}

}
