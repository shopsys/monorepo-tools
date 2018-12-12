<?php

namespace Tests\ShopBundle\Functional\Model\Cart;

use ReflectionClass;
use Shopsys\FrameworkBundle\DataFixtures\Demo\UnitDataFixture;
use Shopsys\FrameworkBundle\Model\Cart\Cart;
use Shopsys\FrameworkBundle\Model\Cart\Item\CartItem;
use Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactory;
use Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Product\Availability\Availability;
use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface;
use Shopsys\ShopBundle\Model\Product\Product;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class CartTest extends TransactionFunctionalTestCase
{
    public function testRemoveItem()
    {
        $em = $this->getEntityManager();
        $productDataFactory = $this->getContainer()->get(ProductDataFactoryInterface::class);

        $customerIdentifier = new CustomerIdentifier('randomString');

        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = 21;
        $vat = new Vat($vatData);
        $availabilityData = new AvailabilityData();
        $availabilityData->dispatchTime = 0;
        $availability = new Availability($availabilityData);
        $productData = $productDataFactory->create();
        $productData->name = [];
        $productData->price = 100;
        $productData->vat = $vat;
        $productData->availability = $availability;
        $productData->unit = $this->getReference(UnitDataFixture::UNIT_PIECES);
        $product1 = Product::create($productData);
        $product2 = Product::create($productData);

        $cartItem1 = new CartItem($customerIdentifier, $product1, 1, '0.0');
        $cartItem2 = new CartItem($customerIdentifier, $product2, 3, '0.0');
        $cartItems = [$cartItem1, $cartItem2];

        $cart = new Cart($cartItems);

        $em->persist($vat);
        $em->persist($availability);
        $em->persist($product1);
        $em->persist($product2);
        $em->persist($cartItem1);
        $em->persist($cartItem2);
        $em->flush();

        $cart->removeItemById($cartItem1->getId());
        $this->assertSame(1, $cart->getItemsCount());
    }

    public function testCleanMakesCartEmpty()
    {
        $product = $this->createProduct();

        $customerIdentifier = new CustomerIdentifier('randomString');

        $cartItem = new CartItem($customerIdentifier, $product, 1, '0.0');
        $cartItems = [$cartItem];
        $cart = new Cart($cartItems);

        $cart->clean();

        $this->assertTrue($cart->isEmpty());
    }

    public function testMergeWithCartReturnsCartWithSummedProducts()
    {
        /** @var \Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactory $cartItemFactory */
        $cartItemFactory = $this->getContainer()->get(CartItemFactory::class);

        $product1 = $this->createProduct();
        $product2 = $this->createProduct();

        // Cart merging is bound to Product Id
        $productReflectionClass = new ReflectionClass(Product::class);
        $idProperty = $productReflectionClass->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($product1, 1);
        $idProperty->setValue($product2, 2);

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

        $mainCart->mergeWithCart($mergingCart, $cartItemFactory, $customerIdentifier1);

        foreach ($mainCart->getItems() as $item) {
            if ($item->getCartIdentifier() !== $customerIdentifier1->getCartIdentifier()) {
                $this->fail('Merged cart contain cartItem with wrong cartIdentifier');
            }
        }

        $this->assertSame(2, $mainCart->getItemsCount());

        $this->assertSame(5, $mainCart->getItems()[0]->getQuantity());
        $this->assertSame(1, $mainCart->getItems()[1]->getQuantity());
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
}
