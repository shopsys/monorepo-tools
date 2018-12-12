<?php

namespace Tests\ShopBundle\Functional\Model\Cart;

use Shopsys\FrameworkBundle\Model\Cart\Cart;
use Shopsys\FrameworkBundle\Model\Cart\CartService;
use Shopsys\FrameworkBundle\Model\Cart\Item\CartItem;
use Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface;
use Shopsys\ShopBundle\Model\Product\Product;
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

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Product
     */
    private function createProduct()
    {
        $productDataFactory = $this->getContainer()->get(ProductDataFactoryInterface::class);

        $price = 100;
        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = 21;
        $vat = new Vat($vatData);

        $productData = $productDataFactory->create();
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
