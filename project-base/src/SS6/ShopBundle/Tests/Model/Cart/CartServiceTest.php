<?php

namespace SS6\ShopBundle\Tests\Model\Security;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\CartItem;
use SS6\ShopBundle\Model\Cart\CartService;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Product\Product;

class CarServicetTest extends PHPUnit_Framework_TestCase {
	
	public function testAddProductToCartInvalidFloatQuantity() {
		$customerIdentifier = new CustomerIdentifier('randomString');
		$cartItems = array();
		$cart = new Cart($cartItems);

		$price = 100;
		$product = new Product('Product 1', null, null, null, null, $price);
		
		$cartService = new CartService();
		$this->setExpectedException('SS6\ShopBundle\Model\Cart\Exception\InvalidQuantityException');
		$cartService->addProductToCart($cart, $customerIdentifier, $product, 1.1);
	}
	
	public function testAddProductToCartInvalidZeroQuantity() {
		$customerIdentifier = new CustomerIdentifier('randomString');
		$cartItems = array();
		$cart = new Cart($cartItems);

		$price = 100;
		$product = new Product('Product 1', null, null, null, null, $price);
		
		$cartService = new CartService();
		$this->setExpectedException('SS6\ShopBundle\Model\Cart\Exception\InvalidQuantityException');
		$cartService->addProductToCart($cart, $customerIdentifier, $product, 0);
	}
	
	public function testAddProductToCartInvalidNegativQuantity() {
		$customerIdentifier = new CustomerIdentifier('randomString');
		$cartItems = array();
		$cart = new Cart($cartItems);

		$price = 100;
		$product = new Product('Product 1', null, null, null, null, $price);
		
		$cartService = new CartService();
		$this->setExpectedException('SS6\ShopBundle\Model\Cart\Exception\InvalidQuantityException');
		$cartService->addProductToCart($cart, $customerIdentifier, $product, -10);
	}
	
	public function testAddProductToCartNewProduct() {
		$customerIdentifier = new CustomerIdentifier('randomString');
		$cartItems = array();
		$cart = new Cart($cartItems);
		
		$price = 100;
		$product = new Product('Product 1', null, null, null, null, $price);

		$quantity = 2;
		
		$cartService = new CartService();
		$result = $cartService->addProductToCart($cart, $customerIdentifier, $product, $quantity);
		$this->assertTrue($result->getIsNew());
		$this->assertEquals($quantity, $result->getAddedQuantity());
	}
	
	public function testAddProductToCartSameProduct() {
		$customerIdentifier = new CustomerIdentifier('randomString');

		$price = 100;
		$product = new Product('Product 1', null, null, null, null, $price);

		$cartItem = new CartItem($customerIdentifier, $product, 1);
		$cartItems = array($cartItem);
		$cart = new Cart($cartItems);
		$quantity = 2;
		
		$cartService = new CartService();
		$result = $cartService->addProductToCart($cart, $customerIdentifier, $product, $quantity);
		$this->assertFalse($result->getIsNew());
		$this->assertEquals($quantity, $result->getAddedQuantity());
	}
	
}
