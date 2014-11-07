<?php

namespace SS6\ShopBundle\TestsCrawler;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;

class PagesResponsesTest extends DatabaseTestCase {

	public function adminPagesProvider() {
		return array(
			['admin/article/list/'],
			['admin/article/edit/1'],
			['admin/article/new/'],
			['admin/customer/list/'],
			['admin/customer/edit/1'],
			['admin/customer/new/'],
			['admin/dashboard/'],
			['admin/mail/template/'],
			['admin/order/edit/1'],
			['admin/order/list/'],
			['admin/order_status/list/'],
			['admin/overview/'],
			['admin/payment/edit/1'],
			['admin/payment/new/'],
			['admin/pricing/group/list/'],
			['admin/product/availability/list/'],
			['admin/product/edit/1'],
			['admin/product/list/'],
			['admin/product/new/'],
			['admin/product/parameter/list/'],
			['admin/slider/item/edit/1'],
			['admin/slider/item/new/'],
			['admin/slider/list/'],
			['admin/superadmin/'],
			['admin/superadmin/errors/'],
			['admin/superadmin/icons/'],
			['admin/superadmin/pricing/'],
			['admin/translation/list/'],
			['admin/transport/edit/1'],
			['admin/transport/new/'],
			['admin/transport_and_payment/list/'],
			['admin/vat/list/'],
		);
	}

	/**
	 * @dataProvider adminPagesProvider
	 */
	public function testAdminPagesStatus200($url) {
		$this->getClient(false, 'admin', 'admin123')->request('GET', $url);
		$this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode());
	}

	public function frontEndPagesProvider() {
		return array(
			['/'],
			['article/detail/1/'],
			['kosik/'],
			['login/'],
			['product/list/'],
			['product/detail/1'],
			['registration/'],

		);
	}

	/**
	 * @dataProvider frontEndPagesProvider
	 */
	public function testFrontEndPages($url) {
		$this->getClient()->request('GET', $url);
		$this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode());
	}

	public function frontEndPagesRegistredUserProvider() {
		return array(
			['customer/edit/'],
			['customer/orders/'],
			['customer/orders/detail/1']
		);
	}

	/**
	 * @dataProvider frontEndPagesRegistredUserProvider
	 */
	public function testFrontEndPagesRegisteredUser($url) {
		$this->getClient(false, 'no-reply@netdevelo.cz', 'user123')->request('GET', $url);
		$this->assertEquals(200, $this->getClient()->getResponse()->getStatusCode());
	}

}
