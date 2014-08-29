<?php

namespace SS6\ShopBundle\Tests\Model\Cart;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\CartService;
use SS6\ShopBundle\Model\Cart\Item\CartItem;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;

class CartServiceTest extends PHPUnit_Framework_TestCase {

	protected function setUp() {
		$this->markTestSkipped();
	}

	public function testAddProductToCartInvalidFloatQuantity() {
		$customerIdentifier = new CustomerIdentifier('randomString');
		$cartItems = array();
		$cart = new Cart($cartItems);

		$price = 100;
		$vat = new Vat(new VatData('vat', 21));
		$product = new Product(new ProductData('Product 1', null, null, null, null, $price, $vat));
		
		$cartService = new CartService();
		$this->setExpectedException('SS6\ShopBundle\Model\Cart\Exception\InvalidQuantityException');
		$cartService->addProductToCart($cart, $customerIdentifier, $product, 1.1);
	}
	
	public function testAddProductToCartInvalidZeroQuantity() {
		$customerIdentifier = new CustomerIdentifier('randomString');
		$cartItems = array();
		$cart = new Cart($cartItems);

		$price = 100;
		$vat = new Vat(new VatData('vat', 21));
		$product = new Product(new ProductData('Product 1', null, null, null, null, $price, $vat));
		
		$cartService = new CartService();
		$this->setExpectedException('SS6\ShopBundle\Model\Cart\Exception\InvalidQuantityException');
		$cartService->addProductToCart($cart, $customerIdentifier, $product, 0);
	}
	
	public function testAddProductToCartInvalidNegativQuantity() {
		$customerIdentifier = new CustomerIdentifier('randomString');
		$cartItems = array();
		$cart = new Cart($cartItems);

		$price = 100;
		$vat = new Vat(new VatData('vat', 21));
		$product = new Product(new ProductData('Product 1', null, null, null, null, $price, $vat));
		
		$cartService = new CartService();
		$this->setExpectedException('SS6\ShopBundle\Model\Cart\Exception\InvalidQuantityException');
		$cartService->addProductToCart($cart, $customerIdentifier, $product, -10);
	}
	
	public function testAddProductToCartNewProduct() {
		$customerIdentifier = new CustomerIdentifier('randomString');
		$cartItems = array();
		$cart = new Cart($cartItems);
		
		$price = 100;
		$vat = new Vat(new VatData('vat', 21));
		$product = new Product(new ProductData('Product 1', null, null, null, null, $price, $vat));

		$quantity = 2;
		
		$cartService = new CartService();
		$result = $cartService->addProductToCart($cart, $customerIdentifier, $product, $quantity);
		$this->assertTrue($result->getIsNew());
		$this->assertEquals($quantity, $result->getAddedQuantity());
	}
	
	public function testAddProductToCartSameProduct() {
		$customerIdentifier = new CustomerIdentifier('randomString');

		$price = 100;
		$vat = new Vat(new VatData('vat', 21));
		$product = new Product(new ProductData('Product 1', null, null, null, null, $price, $vat));

		$cartItem = new CartItem($customerIdentifier, $product, 1);
		$cartItems = array($cartItem);
		$cart = new Cart($cartItems);
		$quantity = 2;
		
		$cartService = new CartService();
		$result = $cartService->addProductToCart($cart, $customerIdentifier, $product, $quantity);
		$this->assertFalse($result->getIsNew());
		$this->assertEquals($quantity, $result->getAddedQuantity());
	}

	public function testCleanCart() {
		$customerIdentifier = new CustomerIdentifier('randomString');

		$price = 100;
		$vat = new Vat(new VatData('vat', 21));
		$product = new Product(new ProductData('Product 1', null, null, null, null, $price, $vat));

		$cartItem = new CartItem($customerIdentifier, $product, 1);
		$cartItems = array($cartItem);
		$cart = new Cart($cartItems);
		
		$cartService = new CartService();
		$cartService->cleanCart($cart);
		
		$this->assertTrue($cart->isEmpty());
	}

	public function testMergeCarts() {
		$cartService = new CartService();

		$price = 100;
		$vat = new Vat(new VatData('vat', 21));
		$product1 = new Product(new ProductData('Product 1', null, null, null, null, $price, $vat));
		$product2 = new Product(new ProductData('Product 2', null, null, null, null, $price, $vat));

		$sessionId1 = 'abc123';
		$sessionId2 = 'def456';
		$customerIdentifier1 = new CustomerIdentifier($sessionId1);
		$customerIdentifier2 = new CustomerIdentifier($sessionId2);

		$cartItem = new CartItem($customerIdentifier1, $product1, 2);
		$cartItems = array($cartItem);
		$mainCart = new Cart($cartItems);

		$cartItem1 = new CartItem($customerIdentifier2, $product1, 3);
		$cartItem2 = new CartItem($customerIdentifier2, $product2, 1);
		$cartItems = array($cartItem1, $cartItem2);
		$mergingCart = new Cart($cartItems);

		$cartService->mergeCarts($mainCart, $mergingCart, $customerIdentifier1);

		foreach ($mainCart->getItems() as $item) {
			if ($item->getSessionId() !== $customerIdentifier1->getSessionId()) {
				$this->fail('Merged cart contain cartItem with wrong sessionId');
			}
		}

		$this->assertEquals(2, $mergingCart->getItemsCount());
		$this->assertEquals(4, $mergingCart->getQuantity());
		$this->assertSame(4 * $price, $mergingCart->getPrice());
	}
	
}
