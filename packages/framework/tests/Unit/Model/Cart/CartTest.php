<?php

namespace Tests\FrameworkBundle\Unit\Model\Cart;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Cart\Cart;
use Shopsys\FrameworkBundle\Model\Cart\Item\CartItem;
use Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductData;

class CartTest extends TestCase
{
    public function testGetItemsCountZero()
    {
        $cartItems = [];
        $cart = new Cart($cartItems);
        $this->assertSame(0, $cart->getItemsCount());
    }

    public function testGetItemsCount()
    {
        $customerIdentifier = new CustomerIdentifier('randomString');

        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = 21;
        $vat = new Vat($vatData);
        $productData1 = new ProductData();
        $productData1->name = ['cs' => 'Product 1'];
        $productData1->price = 100;
        $productData1->vat = $vat;
        $product1 = Product::create($productData1);

        $productData2 = new ProductData();
        $productData2->name = ['cs' => 'Product 2'];
        $productData2->price = 200;
        $productData2->vat = $vat;
        $product2 = Product::create($productData2);

        $cartItem1 = new CartItem($customerIdentifier, $product1, 1, '0.0');
        $cartItem2 = new CartItem($customerIdentifier, $product2, 3, '0.0');
        $cartItems = [$cartItem1, $cartItem2];

        $cart = new Cart($cartItems);
        $this->assertSame(2, $cart->getItemsCount());
    }

    public function testIsEmpty()
    {
        $cartItems = [];

        $cart = new Cart($cartItems);

        $this->assertTrue($cart->isEmpty());
    }

    public function testIsNotEmpty()
    {
        $customerIdentifier = new CustomerIdentifier('randomString');

        $price = 100;
        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = 21;
        $vat = new Vat($vatData);
        $productData = new ProductData();
        $productData->name = ['cs' => 'Product 1'];
        $productData->price = $price;
        $productData->vat = $vat;
        $product = Product::create($productData);

        $cartItem = new CartItem($customerIdentifier, $product, 1, '0.0');
        $cartItems = [$cartItem];

        $cart = new Cart($cartItems);
        $this->assertFalse($cart->IsEmpty());
    }

    public function testClean()
    {
        $customerIdentifier = new CustomerIdentifier('randomString');

        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = 21;
        $vat = new Vat($vatData);
        $productData1 = new ProductData();
        $productData1->name = ['cs' => 'Product 1'];
        $productData1->price = 100;
        $productData1->vat = $vat;
        $product1 = Product::create($productData1);

        $productData2 = new ProductData();
        $productData2->name = ['cs' => 'Product 2'];
        $productData2->price = 200;
        $productData2->vat = $vat;
        $product2 = Product::create($productData2);

        $cartItem1 = new CartItem($customerIdentifier, $product1, 1, '0.0');
        $cartItem2 = new CartItem($customerIdentifier, $product2, 3, '0.0');
        $cartItems = [$cartItem1, $cartItem2];

        $cart = new Cart($cartItems);
        $cart->clean();

        $this->assertTrue($cart->isEmpty());
    }
}
