<?php

namespace Tests\ShopBundle\Database\Model\Cart;

use Shopsys\FrameworkBundle\DataFixtures\Base\UnitDataFixture;
use Shopsys\FrameworkBundle\Model\Cart\Cart;
use Shopsys\FrameworkBundle\Model\Cart\Item\CartItem;
use Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Product\Availability\Availability;
use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactory;
use Tests\ShopBundle\Test\DatabaseTestCase;

class CartTest extends DatabaseTestCase
{
    public function testRemoveItem()
    {
        $em = $this->getEntityManager();
        $productDataFactory = $this->getContainer()->get(ProductDataFactory::class);

        $customerIdentifier = new CustomerIdentifier('randomString');

        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = 21;
        $vat = new Vat($vatData);
        $availability = new Availability(new AvailabilityData([], 0));
        $productData = $productDataFactory->createDefault();
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
}
