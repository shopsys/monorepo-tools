<?php

namespace SS6\ShopBundle\Tests\Model\Security;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\CartItem;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Product\Product;

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

		$price1 = 100;
		$product1 = new Product('Product 1', null, null, null, null, $price1);
		$price2 = 200;
		$product2 = new Product('Product 2', null, null, null, null, $price2);
		
		$cartItem1 = new CartItem($customerIdentifier, $product1, 1);
		$cartItem2 = new CartItem($customerIdentifier, $product2, 3);
		$cartItems = array($cartItem1, $cartItem2);
		
		$cart = new Cart($cartItems);
		$this->assertEquals(2, $cart->getItemsCount());
	}
	
	public function testGetPrice() {
		$customerIdentifier = new CustomerIdentifier('randomString');
		
		$price1 = 100;
		$product1 = new Product('Product 1', null, null, null, null, $price1);
		$price2 = 200;
		$product2 = new Product('Product 2', null, null, null, null, $price2);
		
		$cartItem1 = new CartItem($customerIdentifier, $product1, 1);
		$cartItem2 = new CartItem($customerIdentifier, $product2, 3);
		$cartItems = array($cartItem1, $cartItem2);
		
		$cart = new Cart($cartItems);
		$this->assertSame(700, $cart->getPrice()); // price can be string, int or float...
	}
	
	public function testGetQuantity() {
		$customerIdentifier = new CustomerIdentifier('randomString');
		
		$price1 = 100;
		$product1 = new Product('Product 1', null, null, null, null, $price1);
		$price2 = 200;
		$product2 = new Product('Product 2', null, null, null, null, $price2);
		
		$cartItem1 = new CartItem($customerIdentifier, $product1, 1);
		$cartItem2 = new CartItem($customerIdentifier, $product2, 3);
		$cartItems = array($cartItem1, $cartItem2);
		
		$cart = new Cart($cartItems);
		$this->assertEquals(4, $cart->getQuantity());
	}
}
