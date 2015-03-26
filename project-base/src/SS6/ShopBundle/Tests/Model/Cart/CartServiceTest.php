<?php

namespace SS6\ShopBundle\Tests\Model\Cart;

use SS6\ShopBundle\Component\Test\FunctionalTestCase;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\Item\CartItem;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;

class CartServiceTest extends FunctionalTestCase {

	public function testAddProductToCartInvalidFloatQuantity() {
		$cartService = $this->getContainer()->get('ss6.shop.cart.cart_service');
		/* @var $cartService \SS6\ShopBundle\Model\Cart\CartService */

		$customerIdentifier = new CustomerIdentifier('randomString');
		$cartItems = [];
		$cart = new Cart($cartItems);

		$price = 100;
		$vat = new Vat(new VatData('vat', 21));
		$product = new Product(new ProductData(['cs' => 'Product 1'], null, null, null, [], $price, $vat));

		$this->setExpectedException('SS6\ShopBundle\Model\Cart\Exception\InvalidQuantityException');
		$cartService->addProductToCart($cart, $customerIdentifier, $product, 1.1);
	}

	public function testAddProductToCartInvalidZeroQuantity() {
		$cartService = $this->getContainer()->get('ss6.shop.cart.cart_service');
		/* @var $cartService \SS6\ShopBundle\Model\Cart\CartService */

		$customerIdentifier = new CustomerIdentifier('randomString');
		$cartItems = [];
		$cart = new Cart($cartItems);

		$price = 100;
		$vat = new Vat(new VatData('vat', 21));
		$product = new Product(new ProductData(['cs' => 'Product 1'], null, null, null, [], $price, $vat));

		$this->setExpectedException('SS6\ShopBundle\Model\Cart\Exception\InvalidQuantityException');
		$cartService->addProductToCart($cart, $customerIdentifier, $product, 0);
	}

	public function testAddProductToCartInvalidNegativeQuantity() {
		$cartService = $this->getContainer()->get('ss6.shop.cart.cart_service');
		/* @var $cartService \SS6\ShopBundle\Model\Cart\CartService */

		$customerIdentifier = new CustomerIdentifier('randomString');
		$cartItems = [];
		$cart = new Cart($cartItems);

		$price = 100;
		$vat = new Vat(new VatData('vat', 21));
		$product = new Product(new ProductData(['cs' => 'Product 1'], null, null, null, [], $price, $vat));

		$this->setExpectedException('SS6\ShopBundle\Model\Cart\Exception\InvalidQuantityException');
		$cartService->addProductToCart($cart, $customerIdentifier, $product, -10);
	}

	public function testAddProductToCartNewProduct() {
		$cartService = $this->getContainer()->get('ss6.shop.cart.cart_service');
		/* @var $cartService \SS6\ShopBundle\Model\Cart\CartService */

		$customerIdentifier = new CustomerIdentifier('randomString');
		$cartItems = [];
		$cart = new Cart($cartItems);

		$price = 100;
		$vat = new Vat(new VatData('vat', 21));
		$product = new Product(new ProductData(['cs' => 'Product 1'], null, null, null, [], $price, $vat));

		$quantity = 2;

		$result = $cartService->addProductToCart($cart, $customerIdentifier, $product, $quantity);
		$this->assertTrue($result->getIsNew());
		$this->assertSame($quantity, $result->getAddedQuantity());
	}

	public function testAddProductToCartSameProduct() {
		$cartService = $this->getContainer()->get('ss6.shop.cart.cart_service');
		/* @var $cartService \SS6\ShopBundle\Model\Cart\CartService */

		$customerIdentifier = new CustomerIdentifier('randomString');

		$price = 100;
		$vat = new Vat(new VatData('vat', 21));
		$product = new Product(new ProductData(['cs' => 'Product 1'], null, null, null, [], $price, $vat));

		$cartItem = new CartItem($customerIdentifier, $product, 1, '0.0');
		$cartItems = [$cartItem];
		$cart = new Cart($cartItems);
		$quantity = 2;

		$result = $cartService->addProductToCart($cart, $customerIdentifier, $product, $quantity);
		$this->assertFalse($result->getIsNew());
		$this->assertSame($quantity, $result->getAddedQuantity());
	}

	public function testCleanCart() {
		$cartService = $this->getContainer()->get('ss6.shop.cart.cart_service');
		/* @var $cartService \SS6\ShopBundle\Model\Cart\CartService */

		$customerIdentifier = new CustomerIdentifier('randomString');

		$price = 100;
		$vat = new Vat(new VatData('vat', 21));
		$product = new Product(new ProductData(['cs' => 'Product 1'], null, null, null, [], $price, $vat));

		$cartItem = new CartItem($customerIdentifier, $product, 1, '0.0');
		$cartItems = [$cartItem];
		$cart = new Cart($cartItems);

		$cartService->cleanCart($cart);

		$this->assertTrue($cart->isEmpty());
	}

	public function testMergeCarts() {
		$cartService = $this->getContainer()->get('ss6.shop.cart.cart_service');
		/* @var $cartService \SS6\ShopBundle\Model\Cart\CartService */

		$price = 100;
		$vat = new Vat(new VatData('vat', 21));
		$product1 = new Product(new ProductData(['cs' => 'Product 1'], null, null, null, [], $price, $vat));
		$product2 = new Product(new ProductData(['cs' => 'Product 2'], null, null, null, [], $price, $vat));

		$sessionId1 = 'abc123';
		$sessionId2 = 'def456';
		$customerIdentifier1 = new CustomerIdentifier($sessionId1);
		$customerIdentifier2 = new CustomerIdentifier($sessionId2);

		$cartItem = new CartItem($customerIdentifier1, $product1, 2, '0.0');
		$cartItems = [$cartItem];
		$mainCart = new Cart($cartItems);

		$cartItem1 = new CartItem($customerIdentifier2, $product1, 3, '0.0');
		$cartItem2 = new CartItem($customerIdentifier2, $product2, 1, '0.0');
		$cartItems = [$cartItem1, $cartItem2];
		$mergingCart = new Cart($cartItems);

		$cartService->mergeCarts($mainCart, $mergingCart, $customerIdentifier1);

		foreach ($mainCart->getItems() as $item) {
			if ($item->getSessionId() !== $customerIdentifier1->getSessionId()) {
				$this->fail('Merged cart contain cartItem with wrong sessionId');
			}
		}

		$this->assertSame(2, $mergingCart->getItemsCount());
	}

}
