<?php

namespace SS6\ShopBundle\TestsCrawler\Localization;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;

class LocalizationListenerTest extends DatabaseTestCase {

	public function testProductDetailLocaleCs() {
		$crawler = $this->getClient()->request('GET', 'product/detail/3');

		$this->assertGreaterThan(
			0,
			$crawler->filter('html:contains("Katalogové číslo")')->count()
		);
	}

	public function testProductDetailLocaleEn() {
		$domain = $this->getContainer()->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */

		$domain->switchDomainById(2);

		$crawler = $this->getClient()->request('GET', 'product/detail/3');

		$this->assertGreaterThan(
			0,
			$crawler->filter('html:contains("Catalogue number")')->count()
		);
	}

}
