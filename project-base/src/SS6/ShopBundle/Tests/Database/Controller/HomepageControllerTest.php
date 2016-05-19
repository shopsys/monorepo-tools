<?php

namespace SS6\ShopBundle\Tests\Database\Controller;

use SS6\ShopBundle\Tests\Test\FunctionalTestCase;

/**
 * @UglyTest
 */
class HomepageControllerTest extends FunctionalTestCase {

	public function testHomepageHttpStatus200() {
		$client = $this->getClient();

		$client->request('GET', '/');
		$code = $client->getResponse()->getStatusCode();

		$this->assertSame(200, $code);
	}

	public function testHomepageHasBodyEnd() {
		$client = $this->getClient();

		$client->request('GET', '/');
		$content = $client->getResponse()->getContent();

		$this->assertRegExp('/<\/body>/ui', $content);
	}

}
