<?php

namespace SS6\ShopBundle\TestsCrawler;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;

class NewProductTest extends DatabaseTestCase {

	public function testCreateOrEditProductProvider() {

		return [['admin/product/new/'], ['admin/product/edit/1']];
	}

	/**
	 * @dataProvider testCreateOrEditProductProvider
	 */
	public function testCreateOrEditProduct($route) {
		$this->authenticateUser('admin', 'admin123');
		$crawler = $this->getClient()->request('GET', $route);
		$form = $crawler->filter('form[name=product]')->form();

		$client = $this->getClient(true);
		$this->authenticateUser('admin', 'admin123');
		$csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('post_type');

		$this->setFormValuesAndToken($form, $csrfToken);

		$client->submit($form);

		$flashMessageBag = $client->getContainer()->get('ss6.shop.flash_message.bag.admin');
		/* @var $flashMessageBag \SS6\ShopBundle\Model\FlashMessage\Bag */

		$this->assertFalse(empty($flashMessageBag->getSuccessMessages()));
		$this->assertTrue(empty($flashMessageBag->getErrorMessages()));
		$this->assertEquals(302, $client->getResponse()->getStatusCode());

	}

	/**
	 * @param Symfony\Component\DomCrawler\Form $form
	 * @param string $csrfToken
	 */
	private function setFormValuesAndToken($form, $csrfToken) {
		$form['product[name]'] = 'testProduct';
		$form['product[showOnDomains]'] = [1];
		$form['product[catnum]'] = '123456';
		$form['product[partno]'] = '123456';
		$form['product[ean]'] = '123456';
		$form['product[description]'] = 'test description';
		$form['product[price]'] = '10000';
		$form['product[vat]']->select(1);
		$form['product[sellingFrom]'] = '1.1.1990';
		$form['product[sellingTo]'] = '1.1.2000';
		$form['product[stockQuantity]'] = '10';
		$form['product[availability]']->select(1);
		$form['product[_token]'] = $csrfToken;
	}

}
