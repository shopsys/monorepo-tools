<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Cart;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Cart\CartFactory;
use SS6\ShopBundle\Model\Cart\Item\CartItemRepository;
use SS6\ShopBundle\Model\Cart\Watcher\CartWatcherFacade;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;

/**
 * @UglyTest
 */
class CartFactoryTest extends PHPUnit_Framework_TestCase {

	public function testGetReturnsTheSameCartForTheSameCustomer() {
		$cartItemRepository = $this->getMockBuilder(CartItemRepository::class)
			->setMethods(['__construct', 'getAllByCustomerIdentifier'])
			->disableOriginalConstructor()
			->getMock();
		$cartItemRepository->expects($this->once())->method('getAllByCustomerIdentifier')->will($this->returnValue([]));

		$cartWatcherFacade = $this->getMockBuilder(CartWatcherFacade::class)
			->setMethods(['__construct', 'checkCartModifications'])
			->disableOriginalConstructor()
			->getMock();
		$cartWatcherFacade->expects($this->any())->method('checkCartModifications');

		$cartFactory = new CartFactory($cartItemRepository, $cartWatcherFacade);

		$sessionId = 'abc123';
		$customerIdentifier1 = new CustomerIdentifier($sessionId);
		$customerIdentifier2 = new CustomerIdentifier($sessionId);

		$cart1 = $cartFactory->get($customerIdentifier1);
		$cart2 = $cartFactory->get($customerIdentifier2);

		$this->assertTrue($cart1 === $cart2);
	}

	public function testGetReturnsDifferentCartsForDifferentCustomers() {
		$cartItemRepository = $this->getMockBuilder(CartItemRepository::class)
			->setMethods(['__construct', 'getAllByCustomerIdentifier'])
			->disableOriginalConstructor()
			->getMock();
		$cartItemRepository->expects($this->exactly(2))->method('getAllByCustomerIdentifier')->will($this->returnValue([]));

		$cartWatcherFacade = $this->getMockBuilder(CartWatcherFacade::class)
			->setMethods(['__construct', 'checkCartModifications'])
			->disableOriginalConstructor()
			->getMock();
		$cartWatcherFacade->expects($this->any())->method('checkCartModifications');

		$cartFactory = new CartFactory($cartItemRepository, $cartWatcherFacade);

		$sessionId1 = 'abc123';
		$sessionId2 = 'def456';
		$customerIdentifier1 = new CustomerIdentifier($sessionId1);
		$customerIdentifier2 = new CustomerIdentifier($sessionId2);

		$cart1 = $cartFactory->get($customerIdentifier1);
		$cart2 = $cartFactory->get($customerIdentifier2);

		$this->assertFalse($cart1 === $cart2);
	}

}
