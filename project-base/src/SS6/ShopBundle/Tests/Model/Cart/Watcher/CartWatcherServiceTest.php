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

	public function testShowErrorOnModifiedItems() {
		$customerIdentifier = new CustomerIdentifier('randomString');

		$vat = new Vat(new VatData('vat', 21));
		$productMock = $this->getMockBuilder(Product::class)
			->setMethods(['getCurrentLocale'])
			->setConstructorArgs([new ProductData(['cs' => 'Product 1'], null, null, null, [], 100, $vat)])
			->getMock();
		$productMock->expects($this->any())->method('getCurrentLocale')->willReturn('cs');

		$productPriceCalculation = $this->getContainer()->get('ss6.shop.product.price_calculation');
		/* @var $productPriceCalculation \SS6\ShopBundle\Model\Product\PriceCalculation */
		$productPrice = $productPriceCalculation->calculatePrice($productMock);
		$cartItem = new CartItem($customerIdentifier, $productMock, 1, $productPrice->getPriceWithVat());
		$cartItems = array($cartItem);
		$cart = new Cart($cartItems);

		$flashMessageFront = $this->getContainer()->get('ss6.shop.flash_message.bag.front');
		/* @var $flashMessageFront \SS6\ShopBundle\Model\FlashMessage\Bag */

		// clear...
		$flashMessageFront->getErrorMessages();
		$flashMessageFront->getInfoMessages();
		$flashMessageFront->getSuccessMessages();

		$cartWatcherService = $this->getContainer()->get('ss6.shop.cart.cart_watcher_service');
		/* @var $cartWatcherService \SS6\ShopBundle\Model\Cart\Watcher\CartWatcherService */

		$cartWatcherService->showErrorOnModifiedItems($cart);
		$this->assertTrue($flashMessageFront->isEmpty());

		$productMock->edit(new ProductData(['cs' => 'Product 1'], null, null, null, [], 200, $vat));
		$cartWatcherService->showErrorOnModifiedItems($cart);
		$this->assertFalse($flashMessageFront->isEmpty());
	}

	public function testGetNotVisibleItemsWithItemWithoutProduct() {
		$cartItemMock = $this->getMockBuilder(CartItem::class)
			->disableOriginalConstructor()
			->setMethods(null)
			->getMock();

		$cartItems = array($cartItemMock);
		$cart = new Cart($cartItems);

		$cartWatcherService = $this->getContainer()->get('ss6.shop.cart.cart_watcher_service');
		/* @var $cartWatcherService \SS6\ShopBundle\Model\Cart\Watcher\CartWatcherService */

		$notVisibleItems = $cartWatcherService->getNotVisibleItems($cart);
		$this->assertCount(1, $notVisibleItems);
	}

}
