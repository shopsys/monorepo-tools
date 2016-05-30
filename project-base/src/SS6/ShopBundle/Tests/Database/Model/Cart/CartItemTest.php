<?php

namespace SS6\ShopBundle\Tests\Database\Model\Cart;

use SS6\ShopBundle\DataFixtures\Base\UnitDataFixture;
use SS6\ShopBundle\Model\Cart\Item\CartItem;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\Availability\Availability;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityData;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

/**
 * @UglyTest
 */
class CartItemTest extends DatabaseTestCase {

	public function testIsSimilarItemAs() {
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
		$em->persist($vat);
		$em->persist($availability);
		$em->persist($product1);
		$em->persist($product2);
		$em->flush();

		$cartItem1 = new CartItem($customerIdentifier, $product1, 1, '0.0');
		$cartItem2 = new CartItem($customerIdentifier, $product1, 3, '0.0');
		$cartItem3 = new CartItem($customerIdentifier, $product2, 1, '0.0');

		$this->assertTrue($cartItem1->isSimilarItemAs($cartItem2));
		$this->assertFalse($cartItem1->isSimilarItemAs($cartItem3));
	}
}
