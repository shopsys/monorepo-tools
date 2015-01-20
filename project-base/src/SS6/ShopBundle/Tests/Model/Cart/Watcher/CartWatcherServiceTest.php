<?php

namespace SS6\ShopBundle\Tests\Model\Cart\Watcher;

use SS6\ShopBundle\Component\Test\FunctionalTestCase;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\Item\CartItem;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;

class CartWatcherServiceTest extends FunctionalTestCase {

	public function testGetModifiedPriceItemsAndUpdatePrices() {
		$customerIdentifier = new CustomerIdentifier('randomString');

		$vat = new Vat(new VatData('vat', 21));
		$productMock = $this->getMockBuilder(Product::class)
			->setMethods(['getCurrentLocale'])
			->setConstructorArgs([new ProductData(['cs' => 'Product 1'], null, null, null, [], 100, $vat)])
			->getMock();
		$productMock->expects($this->any())->method('getCurrentLocale')->willReturn('cs');

		$productPriceCalculationForUser = $this->getContainer()->get('ss6.shop.product.pricing.product_price_calculation_for_user');
		/* @var $productPriceCalculationForUser \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser */
		$productPrice = $productPriceCalculationForUser->calculatePriceForCurrentUser($productMock);
		$cartItem = new CartItem($customerIdentifier, $productMock, 1, $productPrice->getPriceWithVat());
		$cartItems = [$cartItem];
		$cart = new Cart($cartItems);

		$cartWatcherService = $this->getContainer()->get('ss6.shop.cart.cart_watcher_service');
		/* @var $cartWatcherService \SS6\ShopBundle\Model\Cart\Watcher\CartWatcherService */

		$modifiedItems1 = $cartWatcherService->getModifiedPriceItemsAndUpdatePrices($cart);
		$this->assertEmpty($modifiedItems1);

		$productMock->edit(new ProductData(['cs' => 'Product 1'], null, null, null, [], 200, $vat));
		$modifiedItems2 = $cartWatcherService->getModifiedPriceItemsAndUpdatePrices($cart);
		$this->assertNotEmpty($modifiedItems2);

		$modifiedItems3 = $cartWatcherService->getModifiedPriceItemsAndUpdatePrices($cart);
		$this->assertEmpty($modifiedItems3);
	}

	public function testGetNotVisibleItemsWithItemWithoutProduct() {
		$cartItemMock = $this->getMockBuilder(CartItem::class)
			->disableOriginalConstructor()
			->setMethods(null)
			->getMock();

		$cartItems = [$cartItemMock];
		$cart = new Cart($cartItems);

		$cartWatcherService = $this->getContainer()->get('ss6.shop.cart.cart_watcher_service');
		/* @var $cartWatcherService \SS6\ShopBundle\Model\Cart\Watcher\CartWatcherService */

		$notVisibleItems = $cartWatcherService->getNotVisibleItems($cart);
		$this->assertCount(1, $notVisibleItems);
	}

}
