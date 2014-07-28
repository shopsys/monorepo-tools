<?php

namespace SS6\ShopBundle\TestsDb\Model\Cart;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\CartItem;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Product\Product;

class CartTest extends DatabaseTestCase {

	public function testRemoveItem() {
		$em = $this->getEntityManager();

		$customerIdentifier = new CustomerIdentifier('randomString');

		$price1 = 100;
		$product1 = new Product('Product 1', null, null, null, null, $price1);
		$price2 = 200;
		$product2 = new Product('Product 2', null, null, null, null, $price2);

		$cartItem1 = new CartItem($customerIdentifier, $product1, 1);
		$cartItem2 = new CartItem($customerIdentifier, $product2, 3);
		$cartItems = array($cartItem1, $cartItem2);

		$cart = new Cart($cartItems);
		
		$em->persist($product1);
		$em->persist($product2);
		$em->persist($cartItem1);
		$em->persist($cartItem2);
		$em->flush();

		$cart->removeItemById($cartItem1->getId());
		$this->assertEquals(1, $cart->getItemsCount());
	}

}
