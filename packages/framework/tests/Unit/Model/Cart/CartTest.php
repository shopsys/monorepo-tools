<?php

namespace Tests\FrameworkBundle\Unit\Model\Cart;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Cart\Cart;
use Shopsys\FrameworkBundle\Model\Cart\Item\CartItem;
use Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductData;

class CartTest extends TestCase
{
    public function testGetItemsCountZero()
    {
        $customerIdentifier = new CustomerIdentifier('randomString');
        $cart = new Cart($customerIdentifier->getCartIdentifier());

        $this->assertSame(0, $cart->getItemsCount());
    }

    public function testGetItemsCount()
    {
        $customerIdentifier = new CustomerIdentifier('randomString');

        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = '21';
        $vat = new Vat($vatData);
        $productData1 = new ProductData();
        $productData1->name = ['cs' => 'Product 1'];
        $productData1->vat = $vat;
        $product1 = Product::create($productData1, new ProductCategoryDomainFactory());

        $productData2 = new ProductData();
        $productData2->name = ['cs' => 'Product 2'];
        $productData2->vat = $vat;
        $product2 = Product::create($productData2, new ProductCategoryDomainFactory());

        $cart = new Cart($customerIdentifier->getCartIdentifier());

        $cartItem1 = new CartItem($cart, $product1, 1, Money::zero());
        $cart->addItem($cartItem1);

        $cartItem2 = new CartItem($cart, $product2, 3, Money::zero());
        $cart->addItem($cartItem2);

        $this->assertSame(2, $cart->getItemsCount());
    }

    public function testIsEmpty()
    {
        $customerIdentifier = new CustomerIdentifier('randomString');

        $cart = new Cart($customerIdentifier->getCartIdentifier());

        $this->assertTrue($cart->isEmpty());
    }

    public function testIsNotEmpty()
    {
        $customerIdentifier = new CustomerIdentifier('randomString');

        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = '21';
        $vat = new Vat($vatData);
        $productData = new ProductData();
        $productData->name = ['cs' => 'Product 1'];
        $productData->vat = $vat;
        $product = Product::create($productData, new ProductCategoryDomainFactory());

        $cart = new Cart($customerIdentifier->getCartIdentifier());

        $cartItem = new CartItem($cart, $product, 1, Money::zero());
        $cart->addItem($cartItem);

        $this->assertFalse($cart->isEmpty());
    }

    public function testClean()
    {
        $customerIdentifier = new CustomerIdentifier('randomString');

        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = '21';
        $vat = new Vat($vatData);
        $productData1 = new ProductData();
        $productData1->name = ['cs' => 'Product 1'];
        $productData1->vat = $vat;
        $product1 = Product::create($productData1, new ProductCategoryDomainFactory());

        $productData2 = new ProductData();
        $productData2->name = ['cs' => 'Product 2'];
        $productData2->vat = $vat;
        $product2 = Product::create($productData2, new ProductCategoryDomainFactory());

        $cart = new Cart($customerIdentifier->getCartIdentifier());

        $cartItem1 = new CartItem($cart, $product1, 1, Money::zero());
        $cart->addItem($cartItem1);
        $cartItem2 = new CartItem($cart, $product2, 3, Money::zero());
        $cart->addItem($cartItem2);

        $cart->clean();

        $this->assertTrue($cart->isEmpty());
    }
}
