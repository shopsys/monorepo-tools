<?php

namespace Tests\ShopBundle\Database\Model\Cart;

use Shopsys\FrameworkBundle\Model\Cart\Cart;
use Shopsys\FrameworkBundle\Model\Cart\CartService;
use Shopsys\FrameworkBundle\Model\Cart\Item\CartItem;
use Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactory;
use Tests\ShopBundle\Test\FunctionalTestCase;

class CartServiceTest extends FunctionalTestCase
{
    public function testCannotAddProductFloatQuantityToCart()
    {
        $cartService = $this->getCartService();
        $product = $this->createProduct();

        $customerIdentifier = new CustomerIdentifier('randomString');

        $cartItems = [];
        $cart = new Cart($cartItems);

        $this->expectException('Shopsys\FrameworkBundle\Model\Cart\Exception\InvalidQuantityException');
        $cartService->addProductToCart($cart, $customerIdentifier, $product, 1.1);
    }

    public function testCannotAddProductZeroQuantityToCart()
    {
        $cartService = $this->getCartService();
        $product = $this->createProduct();

        $customerIdentifier = new CustomerIdentifier('randomString');

        $cartItems = [];
        $cart = new Cart($cartItems);

        $this->expectException('Shopsys\FrameworkBundle\Model\Cart\Exception\InvalidQuantityException');
        $cartService->addProductToCart($cart, $customerIdentifier, $product, 0);
    }

    public function testCannotAddProductNegativeQuantityToCart()
    {
        $cartService = $this->getCartService();
        $product = $this->createProduct();

        $customerIdentifier = new CustomerIdentifier('randomString');

        $cartItems = [];
        $cart = new Cart($cartItems);

        $this->expectException('Shopsys\FrameworkBundle\Model\Cart\Exception\InvalidQuantityException');
        $cartService->addProductToCart($cart, $customerIdentifier, $product, -10);
    }

    public function testAddProductToCartMarksAddedProductAsNew()
    {
        $cartService = $this->getCartService();
        $product = $this->createProduct();

        $customerIdentifier = new CustomerIdentifier('randomString');

        $cartItems = [];
        $cart = new Cart($cartItems);

        $quantity = 2;

        $result = $cartService->addProductToCart($cart, $customerIdentifier, $product, $quantity);
        $this->assertTrue($result->getIsNew());
    }

    public function testAddProductToCartMarksRepeatedlyAddedProductAsNotNew()
    {
        $cartService = $this->getCartService();
        $product = $this->createProduct();

        $customerIdentifier = new CustomerIdentifier('randomString');

        $cartItem = new CartItem($customerIdentifier, $product, 1, '0.0');
        $cartItems = [$cartItem];
        $cart = new Cart($cartItems);

        $quantity = 2;

        $result = $cartService->addProductToCart($cart, $customerIdentifier, $product, $quantity);
        $this->assertFalse($result->getIsNew());
    }

    public function testAddProductResultContainsAddedProductQuantity()
    {
        $cartService = $this->getCartService();
        $product = $this->createProduct();

        $customerIdentifier = new CustomerIdentifier('randomString');

        $cartItems = [];
        $cart = new Cart($cartItems);

        $quantity = 2;

        $result = $cartService->addProductToCart($cart, $customerIdentifier, $product, $quantity);
        $this->assertSame($quantity, $result->getAddedQuantity());
    }

    public function testAddProductResultDoesNotContainPreviouslyAddedProductQuantity()
    {
        $cartService = $this->getCartService();
        $product = $this->createProduct();

        $customerIdentifier = new CustomerIdentifier('randomString');

        $cartItem = new CartItem($customerIdentifier, $product, 1, '0.0');
        $cartItems = [$cartItem];
        $cart = new Cart($cartItems);

        $quantity = 2;

        $result = $cartService->addProductToCart($cart, $customerIdentifier, $product, $quantity);
        $this->assertSame($quantity, $result->getAddedQuantity());
    }

    public function testCleanCartMakesCartEmpty()
    {
        $cartService = $this->getCartService();
        $product = $this->createProduct();

        $customerIdentifier = new CustomerIdentifier('randomString');

        $cartItem = new CartItem($customerIdentifier, $product, 1, '0.0');
        $cartItems = [$cartItem];
        $cart = new Cart($cartItems);

        $cartService->cleanCart($cart);

        $this->assertTrue($cart->isEmpty());
    }

    public function testMergeCartsReturnsCartWithSummedProducts()
    {
        $cartService = $this->getCartService();

        $product1 = $this->createProduct();
        $product2 = $this->createProduct();

        $cartIdentifier1 = 'abc123';
        $cartIdentifier2 = 'def456';
        $customerIdentifier1 = new CustomerIdentifier($cartIdentifier1);
        $customerIdentifier2 = new CustomerIdentifier($cartIdentifier2);

        $cartItem = new CartItem($customerIdentifier1, $product1, 2, '0.0');
        $cartItems = [$cartItem];
        $mainCart = new Cart($cartItems);

        $cartItem1 = new CartItem($customerIdentifier2, $product1, 3, '0.0');
        $cartItem2 = new CartItem($customerIdentifier2, $product2, 1, '0.0');
        $cartItems = [$cartItem1, $cartItem2];
        $mergingCart = new Cart($cartItems);

        $cartService->mergeCarts($mainCart, $mergingCart, $customerIdentifier1);

        foreach ($mainCart->getItems() as $item) {
            if ($item->getCartIdentifier() !== $customerIdentifier1->getCartIdentifier()) {
                $this->fail('Merged cart contain cartItem with wrong cartIdentifier');
            }
        }

        $this->assertSame(2, $mergingCart->getItemsCount());
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    private function createProduct()
    {
        $productDataFactory = $this->getContainer()->get(ProductDataFactory::class);

        $price = 100;
        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = 21;
        $vat = new Vat($vatData);

        $productData = $productDataFactory->createDefault();
        $productData->name = ['cs' => 'Any name'];
        $productData->price = $price;
        $productData->vat = $vat;
        $product = Product::create($productData);

        return $product;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Cart\CartService
     */
    private function getCartService()
    {
        return $this->getContainer()->get(CartService::class);
    }
}
