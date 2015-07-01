<?php

namespace SS6\ShopBundle\Tests\Crawler;

use SS6\ShopBundle\Tests\Test\FunctionalTestCase;

class AdminAccessRightsTest extends FunctionalTestCase {

	public function adminAccessDeniedProvider() {
		return [
			['admin/superadmin/modules/'],
			['admin/superadmin/icons/'],
			['admin/superadmin/errors/'],
			['admin/superadmin/pricing/'],
		];
	}

	/**
	 * @dataProvider adminAccessDeniedProvider
	 */
	public function testAdminAccessDenied($route) {
		$client = $this->getClient(true, 'admin', 'admin123');
		$client->request('GET', $route);
		$this->assertSame(403, $client->getResponse()->getStatusCode());
	}

}
