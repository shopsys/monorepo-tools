<?php

namespace SS6\ShopBundle\TestsDb\Controller\DefaultController;

use SS6\ShopBundle\Component\Test\FunctionalTestCase;

class DefaultControllerTest extends FunctionalTestCase {

	protected function setUp() {
		$domain = $this->getContainer()->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */
		$domain->switchDomainById(1);
	}

	protected function tearDown() {
		$domain = $this->getContainer()->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */
		$domain->revertDomain();
	}

	public function testHomepageHttpStatus200() {
		$client = $this->getClient();

		$client->request('GET', '/');
		$code = $client->getResponse()->getStatusCode();

		$this->assertEquals(200, $code);
	}

	public function testHomepageHasBodyEnd() {
		$client = $this->getClient();

		$client->request('GET', '/');
		$content = $client->getResponse()->getContent();

		$this->assertRegExp('/<\/body>/ui', $content);
	}

}
