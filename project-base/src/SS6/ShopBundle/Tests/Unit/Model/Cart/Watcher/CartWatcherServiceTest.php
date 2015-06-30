<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Cart\Watcher;

use SS6\ShopBundle\DataFixtures\Base\PricingGroupDataFixture;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\Item\CartItem;
use SS6\ShopBundle\Model\Cart\Watcher\CartWatcherService;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;
use SS6\ShopBundle\Model\Product\ProductVisibility;
use SS6\ShopBundle\Model\Product\ProductVisibilityRepository;
use SS6\ShopBundle\Tests\Test\FunctionalTestCase;

class CartWatcherServiceTest extends FunctionalTestCase {

	public function testGetModifiedPriceItemsAndUpdatePrices() {
		$customerIdentifier = new CustomerIdentifier('randomString');

		$vat = new Vat(new VatData('vat', 21));
		$productMock = $this->getMockBuilder(Product::class)
			->setMethods(['getCurrentLocale'])
			->setConstructorArgs([new ProductData(['cs' => 'Product 1'], null, null, null, 100, $vat)])
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

		$productMock->edit(new ProductData(['cs' => 'Product 1'], null, null, null, 200, $vat));
		$modifiedItems2 = $cartWatcherService->getModifiedPriceItemsAndUpdatePrices($cart);
		$this->assertNotEmpty($modifiedItems2);

		$modifiedItems3 = $cartWatcherService->getModifiedPriceItemsAndUpdatePrices($cart);
		$this->assertEmpty($modifiedItems3);
	}

	public function testGetNotListableItemsWithItemWithoutProduct() {
		$cartItemMock = $this->getMockBuilder(CartItem::class)
			->disableOriginalConstructor()
			->setMethods(null)
			->getMock();

		$expectedPricingGroup = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);
		$currentCustomerMock = $this->getMockBuilder(CurrentCustomer::class)
			->disableOriginalConstructor()
			->setMethods(['getPricingGroup'])
			->getMock();
		$currentCustomerMock
			->expects($this->any())
			->method('getPricingGroup')
			->willReturn($expectedPricingGroup);

		$cartItems = [$cartItemMock];
		$cart = new Cart($cartItems);

		$cartWatcherService = $this->getContainer()->get('ss6.shop.cart.cart_watcher_service');
		/* @var $cartWatcherService \SS6\ShopBundle\Model\Cart\Watcher\CartWatcherService */

		$notListableItems = $cartWatcherService->getNotListableItems($cart, $currentCustomerMock);
		$this->assertCount(1, $notListableItems);
	}

	public function testGetNotListableItemsWithVisibleButNotSellableProduct() {
		$vat = new Vat(new VatData('vat', 21));
		$productMock = $this->getMockBuilder(Product::class)
			->setMethods(['getCurrentLocale'])
			->setConstructorArgs([new ProductData(['cs' => 'Product 1'], null, null, null, 100, $vat, null, null, false)])
			->getMock();

		$cartItemMock = $this->getMockBuilder(CartItem::class)
			->disableOriginalConstructor()
			->setMethods(['getProduct'])
			->getMock();
		$cartItemMock
			->expects($this->any())
			->method('getProduct')
			->willReturn($productMock);

		$expectedPricingGroup = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);
		$currentCustomerMock = $this->getMockBuilder(CurrentCustomer::class)
			->disableOriginalConstructor()
			->setMethods(['getPricingGroup'])
			->getMock();
		$currentCustomerMock
			->expects($this->any())
			->method('getPricingGroup')
			->willReturn($expectedPricingGroup);

		$productVisibilityMock = $this->getMockBuilder(ProductVisibility::class)
			->disableOriginalConstructor()
			->setMethods(['isVisible'])
			->getMock();
		$productVisibilityMock
			->expects($this->any())
			->method('isVisible')
			->willReturn(true);

		$productVisibilityRepositoryMock = $this->getMockBuilder(ProductVisibilityRepository::class)
			->disableOriginalConstructor()
			->setMethods(['getProductVisibility'])
			->getMock();
		$productVisibilityRepositoryMock
			->expects($this->any())
			->method('getProductVisibility')
			->willReturn($productVisibilityMock);

		$productPriceCalculationForUser = $this->getContainer()->get(ProductPriceCalculationForUser::class);
		$domain = $this->getContainer()->get(Domain::class);

		$cartWatcherService = new CartWatcherService($productPriceCalculationForUser, $productVisibilityRepositoryMock, $domain);

		$cartItems = [$cartItemMock];
		$cart = new Cart($cartItems);

		$notListableItems = $cartWatcherService->getNotListableItems($cart, $currentCustomerMock);
		$this->assertCount(1, $notListableItems);
	}
}
