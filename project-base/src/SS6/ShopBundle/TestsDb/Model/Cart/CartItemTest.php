<?php

namespace SS6\ShopBundle\TestsDb\Model\Cart;

use SS6\ShopBundle\Model\Cart\CartItem;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Component\Test\DatabaseTestCase;

class CartItemTest extends DatabaseTestCase {
	
	
	public function testIsSimilarItemAs() {
		$em = $this->getEntityManager();
		
		$customerIdentifier = new CustomerIdentifier('randomString');

		$price = 100;
		$product1 = new Product('Product 1', null, null, null, null, $price);
		$product2 = new Product('Product 2', null, null, null, null, $price);
		$em->persist($product1);
		$em->persist($product2);
		$em->flush();
		
		$cartItem1 = new CartItem($customerIdentifier, $product1, 1);
		$cartItem2 = new CartItem($customerIdentifier, $product1, 3);
		$cartItem3 = new CartItem($customerIdentifier, $product2, 1);

		$this->assertTrue($cartItem1->isSimilarItemAs($cartItem2));
		$this->assertFalse($cartItem1->isSimilarItemAs($cartItem3));
	}
}
