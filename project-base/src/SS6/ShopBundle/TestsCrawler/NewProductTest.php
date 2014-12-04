<?php

namespace SS6\ShopBundle\TestsCrawler;

use SS6\ShopBundle\Component\Test\FunctionalTestCase;
use SS6\ShopBundle\DataFixtures\Base\AvailabilityDataFixture;
use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;
use SS6\ShopBundle\Form\Admin\Product\ProductFormType;

class NewProductTest extends FunctionalTestCase {

	public function testCreateOrEditProductProvider() {

		return [['admin/product/new/'], ['admin/product/edit/1']];
	}

	/**
	 * @dataProvider testCreateOrEditProductProvider
	 */
	public function testCreateOrEditProduct($route) {
		$client1 = $this->getClient(false, 'admin', 'admin123');
		$crawler = $client1->request('GET', $route);
		$form = $crawler->filter('form[name=product]')->form();

		$client2 = $this->getClient(true, 'admin', 'admin123');
		$em2 = $client2->getContainer()->get('doctrine.orm.entity_manager');
		/* @var $em2 \Doctrine\ORM\EntityManager */

		$em2->beginTransaction();
		$csrfToken = $client2->getContainer()->get('form.csrf_provider')->generateCsrfToken(ProductFormType::INTENTION);
		$this->setFormValuesAndToken($form, $csrfToken);
		$client2->submit($form);
		$em2->rollback();

		$flashMessageBag = $client2->getContainer()->get('ss6.shop.flash_message.bag.admin');
		/* @var $flashMessageBag \SS6\ShopBundle\Model\FlashMessage\Bag */

		$this->assertFalse(empty($flashMessageBag->getSuccessMessages()));
		$this->assertTrue(empty($flashMessageBag->getErrorMessages()));
		$this->assertEquals(302, $client2->getResponse()->getStatusCode());
	}

	/**
	 * @param \Symfony\Component\DomCrawler\Form $form
	 * @param string $csrfToken
	 */
	private function setFormValuesAndToken($form, $csrfToken) {
		$form['product[name][cs]'] = 'testProduct';
		$form['product[showOnDomains]'] = [1];
		$form['product[catnum]'] = '123456';
		$form['product[partno]'] = '123456';
		$form['product[ean]'] = '123456';
		$form['product[description][cs]'] = 'test description';
		$form['product[price]'] = '10000';
		$form['product[vat]']->select($this->getReference(VatDataFixture::VAT_ZERO)->getId());
		$form['product[sellingFrom]'] = '1.1.1990';
		$form['product[sellingTo]'] = '1.1.2000';
		$form['product[stockQuantity]'] = '10';
		$form['product[availability]']->select($this->getReference(AvailabilityDataFixture::IN_STOCK)->getId());
		$form['product[_token]'] = $csrfToken;
	}

}
