<?php

namespace SS6\ShopBundle\Tests\Model\Cart;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\Item\CartItem;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;

class CartTest extends PHPUnit_Framework_TestCase {

	public function testGetItemsCountZero() {
		$cartItems = [];
		$cart = new Cart($cartItems);
		$this->assertSame(0, $cart->getItemsCount());
	}

	public function testGetItemsCount() {
		$customerIdentifier = new CustomerIdentifier('randomString');

		$vat = new Vat(new VatData('vat', 21));
		$price1 = 100;
		$product1 = new Product(new ProductData(['cs' => 'Product 1'], null, null, null, [], $price1, $vat));
		$price2 = 200;
		$product2 = new Product(new ProductData(['cs' => 'Product 2'], null, null, null, [], $price2, $vat));

		$cartItem1 = new CartItem($customerIdentifier, $product1, 1, '0.0');
		$cartItem2 = new CartItem($customerIdentifier, $product2, 3, '0.0');
		$cartItems = [$cartItem1, $cartItem2];

		$cart = new Cart($cartItems);
		$this->assertSame(2, $cart->getItemsCount());
	}

	public function testIsEmpty() {
		$cartItems = [];

		$cart = new Cart($cartItems);

		$this->assertTrue($cart->isEmpty());
	}

	public function testIsNotEmpty() {
		$customerIdentifier = new CustomerIdentifier('randomString');

		$price = 100;
		$vat = new Vat(new VatData('vat', 21));
		$product = new Product(new ProductData(['cs' => 'Product 1'], null, null, null, [], $price, $vat));

		$cartItem = new CartItem($customerIdentifier, $product, 1, '0.0');
		$cartItems = [$cartItem];

		$cart = new Cart($cartItems);
		$this->assertFalse($cart->IsEmpty());
	}

	public function testClean() {
		$customerIdentifier = new CustomerIdentifier('randomString');

		$vat = new Vat(new VatData('vat', 21));
		$price1 = 100;
		$product1 = new Product(new ProductData(['cs' => 'Product 1'], null, null, null, [], $price1, $vat));
		$price2 = 200;
		$product2 = new Product(new ProductData(['cs' => 'Product 2'], null, null, null, [], $price2, $vat));

		$cartItem1 = new CartItem($customerIdentifier, $product1, 1, '0.0');
		$cartItem2 = new CartItem($customerIdentifier, $product2, 3, '0.0');
		$cartItems = [$cartItem1, $cartItem2];

		$cart = new Cart($cartItems);
		$cart->clean();

		$this->assertTrue($cart->isEmpty());
	}

}
