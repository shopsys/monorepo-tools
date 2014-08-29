<?php

namespace SS6\ShopBundle\TestsDb\Model\Cart;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\Item\CartItem;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;

class CartTest extends DatabaseTestCase {

	public function testRemoveItem() {
		$em = $this->getEntityManager();

		$customerIdentifier = new CustomerIdentifier('randomString');

		$price1 = 100;
		$vat1 = new Vat(new VatData('vat', 21));
		$product1 = new Product(new ProductData('Product 1', null, null, null, null, $price1, $vat1));
		$price2 = 200;
		$vat2 = new Vat(new VatData('vat', 21));
		$product2 = new Product(new ProductData('Product 2', null, null, null, null, $price2, $vat2));

		$cartItem1 = new CartItem($customerIdentifier, $product1, 1, '0.0');
		$cartItem2 = new CartItem($customerIdentifier, $product2, 3, '0.0');
		$cartItems = array($cartItem1, $cartItem2);

		$cart = new Cart($cartItems);

		$em->persist($vat1);
		$em->persist($vat2);
		$em->persist($product1);
		$em->persist($product2);
		$em->persist($cartItem1);
		$em->persist($cartItem2);
		$em->flush();

		$cart->removeItemById($cartItem1->getId());
		$this->assertEquals(1, $cart->getItemsCount());
	}

}
