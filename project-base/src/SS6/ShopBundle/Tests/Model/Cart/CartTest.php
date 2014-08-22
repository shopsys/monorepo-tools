<?php

namespace SS6\ShopBundle\Tests\Model\Cart;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\CartItem;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Pricing\Vat;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;

class CartTest extends PHPUnit_Framework_TestCase {
	
	public function testGetItemsCountZero() {
		$cartItems = array();
		$cart = new Cart($cartItems);
		$this->assertEquals(0, $cart->getItemsCount());
	}
	
	public function testGetPriceZero() {
		$cartItems = array();
		$cart = new Cart($cartItems);
		$this->assertSame(0, $cart->getPrice()); // price can be string, int or float...
	}
	
	public function testGetQuantityZero() {
		$cartItems = array();
		$cart = new Cart($cartItems);
		$this->assertSame(0, $cart->getQuantity());
	}
	
	public function testGetItemsCount() {
		$customerIdentifier = new CustomerIdentifier('randomString');

		$vat = new Vat('vat', 21);
		$price1 = 100;
		$product1 = new Product(new ProductData('Product 1', null, null, null, null, $price1, $vat));
		$price2 = 200;
		$product2 = new Product(new ProductData('Product 2', null, null, null, null, $price2, $vat));
		
		$cartItem1 = new CartItem($customerIdentifier, $product1, 1);
		$cartItem2 = new CartItem($customerIdentifier, $product2, 3);
		$cartItems = array($cartItem1, $cartItem2);
		
		$cart = new Cart($cartItems);
		$this->assertEquals(2, $cart->getItemsCount());
	}
	
	public function testGetPrice() {
		$customerIdentifier = new CustomerIdentifier('randomString');
		
		$price1 = 100;
		$vat1 = new Vat('vat', 21);
		$product1 = new Product(new ProductData('Product 1', null, null, null, null, $price1, $vat1));
		$price2 = 200;
		$vat2 = new Vat('vat', 21);
		$product2 = new Product(new ProductData('Product 2', null, null, null, null, $price2, $vat2));
		
		$cartItem1 = new CartItem($customerIdentifier, $product1, 1);
		$cartItem2 = new CartItem($customerIdentifier, $product2, 3);
		$cartItems = array($cartItem1, $cartItem2);
		
		$cart = new Cart($cartItems);
		$this->assertSame(700, $cart->getPrice()); // price can be string, int or float...
	}
	
	public function testGetQuantity() {
		$customerIdentifier = new CustomerIdentifier('randomString');

		$vat = new Vat('vat', 21);
		$price1 = 100;
		$product1 = new Product(new ProductData('Product 1', null, null, null, null, $price1, $vat));
		$price2 = 200;
		$product2 = new Product(new ProductData('Product 2', null, null, null, null, $price2, $vat));
		
		$cartItem1 = new CartItem($customerIdentifier, $product1, 1);
		$cartItem2 = new CartItem($customerIdentifier, $product2, 3);
		$cartItems = array($cartItem1, $cartItem2);
		
		$cart = new Cart($cartItems);
		$this->assertEquals(4, $cart->getQuantity());
	}

	public function testIsEmpty() {
		$cartItems = array();

		$cart = new Cart($cartItems);

		$this->assertTrue($cart->isEmpty());
	}

	public function testIsNotEmpty() {
		$customerIdentifier = new CustomerIdentifier('randomString');

		$price = 100;
		$vat = new Vat('vat', 21);
		$product = new Product(new ProductData('Product 1', null, null, null, null, $price, $vat));

		$cartItem = new CartItem($customerIdentifier, $product, 1);
		$cartItems = array($cartItem);

		$cart = new Cart($cartItems);
		$this->assertFalse($cart->IsEmpty());
	}

	public function testClean() {
		$customerIdentifier = new CustomerIdentifier('randomString');

		$vat = new Vat('vat', 21);
		$price1 = 100;
		$product1 = new Product(new ProductData('Product 1', null, null, null, null, $price1, $vat));
		$price2 = 200;
		$product2 = new Product(new ProductData('Product 2', null, null, null, null, $price2, $vat));

		$cartItem1 = new CartItem($customerIdentifier, $product1, 1);
		$cartItem2 = new CartItem($customerIdentifier, $product2, 3);
		$cartItems = array($cartItem1, $cartItem2);

		$cart = new Cart($cartItems);
		$cart->clean();

		$this->assertEquals(0, $cart->getQuantity());
		$this->assertEquals(0, $cart->getPrice());
		$this->assertTrue($cart->isEmpty());
	}

}
