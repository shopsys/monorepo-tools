<?php

namespace Shopsys\ShopBundle\Tests\Database\Model\Cart;

use Shopsys\ShopBundle\DataFixtures\Base\UnitDataFixture;
use Shopsys\ShopBundle\Model\Cart\Cart;
use Shopsys\ShopBundle\Model\Cart\Item\CartItem;
use Shopsys\ShopBundle\Model\Customer\CustomerIdentifier;
use Shopsys\ShopBundle\Model\Pricing\Vat\Vat;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatData;
use Shopsys\ShopBundle\Model\Product\Availability\Availability;
use Shopsys\ShopBundle\Model\Product\Availability\AvailabilityData;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductData;
use Shopsys\ShopBundle\Tests\Test\DatabaseTestCase;

class CartTest extends DatabaseTestCase {

    public function testRemoveItem() {
        $em = $this->getEntityManager();

        $customerIdentifier = new CustomerIdentifier('randomString');

        $vat = new Vat(new VatData('vat', 21));
        $availability = new Availability(new AvailabilityData([], 0));
        $productData = new ProductData();
        $productData->name = [];
        $productData->price = 100;
        $productData->vat = $vat;
        $productData->availability = $availability;
        $productData->unit = $this->getReference(UnitDataFixture::PCS);
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
