<?php

namespace SS6\ShopBundle\TestsDb\Model\Cart;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Cart\Item\CartItem;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;

class CartItemTest extends DatabaseTestCase {


	public function testIsSimilarItemAs() {
		$em = $this->getEntityManager();

		$customerIdentifier = new CustomerIdentifier('randomString');

		$price = 100;
		$vat = new Vat(new VatData('vat', 21));
		$product1 = new Product(new ProductData(['cs' => 'Product 1'], null, null, null, [], $price, $vat));
		$product2 = new Product(new ProductData(['cs' => 'Product 2'], null, null, null, [], $price, $vat));
		$em->persist($vat);
		$em->persist($product1);
		$em->persist($product2);
		$em->flush();

		$cartItem1 = new CartItem($customerIdentifier, $product1, 1, '0.0');
		$cartItem2 = new CartItem($customerIdentifier, $product1, 3, '0.0');
		$cartItem3 = new CartItem($customerIdentifier, $product2, 1, '0.0');

		$this->assertTrue($cartItem1->isSimilarItemAs($cartItem2));
		$this->assertFalse($cartItem1->isSimilarItemAs($cartItem3));
	}
}
