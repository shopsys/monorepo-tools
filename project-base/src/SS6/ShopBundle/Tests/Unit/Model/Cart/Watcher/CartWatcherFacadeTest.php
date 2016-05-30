<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Cart\Watcher;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\FlashMessage\FlashMessageSender;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\Item\CartItem;
use SS6\ShopBundle\Model\Cart\Watcher\CartWatcherFacade;
use SS6\ShopBundle\Model\Cart\Watcher\CartWatcherService;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Order\PromoCode\CurrentPromoCodeFacade;

/**
 * @UglyTest
 */
class CartWatcherFacadeTest extends PHPUnit_Framework_TestCase {

	public function testCheckCartModificationsRemovePromoCodeOnEmptyCart() {
		$flashMessageSenderMock = $this->getMock(FlashMessageSender::class, [], [], '', false);
		$currentCustomerMock = $this->getMock(CurrentCustomer::class, [], [], '', false);
		$emMock = $this->getMock(EntityManager::class, ['flush'], [], '', false);
		$emMock->expects($this->any())->method('flush');
		$cartWatcherServiceMock = $this->getMock(
			CartWatcherService::class,
			['getModifiedPriceItemsAndUpdatePrices', 'getNotListableItems'],
			[],
			'',
			false
		);
		$cartWatcherServiceMock->expects($this->atLeastOnce())->method('getModifiedPriceItemsAndUpdatePrices')->willReturn([]);
		$cartWatcherServiceMock->expects($this->atLeastOnce())->method('getNotListableItems')->willReturn([]);

		$currentPromoCodeFacadeMock = $this->getMock(CurrentPromoCodeFacade::class, ['removeEnteredPromoCode'], [], '', false);
		$currentPromoCodeFacadeMock->expects($this->once())->method('removeEnteredPromoCode');

		$cartWatcherFacade = new CartWatcherFacade(
			$flashMessageSenderMock,
			$emMock,
			$cartWatcherServiceMock,
			$currentCustomerMock,
			$currentPromoCodeFacadeMock
		);

		$cart = new Cart([]);

		$cartWatcherFacade->checkCartModifications($cart);
	}

	public function testCheckCartModificationsNotRemovePromoCode() {
		$flashMessageSenderMock = $this->getMock(FlashMessageSender::class, [], [], '', false);
		$currentCustomerMock = $this->getMock(CurrentCustomer::class, [], [], '', false);
		$emMock = $this->getMock(EntityManager::class, ['flush'], [], '', false);
		$emMock->expects($this->any())->method('flush');
		$cartWatcherServiceMock = $this->getMock(
			CartWatcherService::class,
			['getModifiedPriceItemsAndUpdatePrices', 'getNotListableItems'],
			[],
			'',
			false
		);
		$cartWatcherServiceMock->expects($this->atLeastOnce())->method('getModifiedPriceItemsAndUpdatePrices')->willReturn([]);
		$cartWatcherServiceMock->expects($this->atLeastOnce())->method('getNotListableItems')->willReturn([]);

		$currentPromoCodeFacadeMock = $this->getMock(CurrentPromoCodeFacade::class, ['removeEnteredPromoCode'], [], '', false);
		$currentPromoCodeFacadeMock->expects($this->never())->method('removeEnteredPromoCode');

		$cartWatcherFacade = new CartWatcherFacade(
			$flashMessageSenderMock,
			$emMock,
			$cartWatcherServiceMock,
			$currentCustomerMock,
			$currentPromoCodeFacadeMock
		);
		$cartItem = $this->getMock(CartItem::class, [], [], '', false);
		$cart = new Cart([$cartItem]);

		$cartWatcherFacade->checkCartModifications($cart);
	}

}
