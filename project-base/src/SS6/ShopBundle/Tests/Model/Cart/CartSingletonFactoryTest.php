<?php

namespace SS6\ShopBundle\Tests\Model\Cart;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Cart\CartItemRepository;
use SS6\ShopBundle\Model\Cart\CartSingletonFactory;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;

class CartSingletonFactoryTest extends PHPUnit_Framework_TestCase {
	
	public function testGetSameCart() {
		$cartItemRepository = $this->getMockBuilder(CartItemRepository::class)
			->setMethods(array('__construct', 'findAllByCustomerIdentifier'))
			->disableOriginalConstructor()
			->getMock();
		$cartItemRepository->expects($this->once())->method('findAllByCustomerIdentifier')->will($this->returnValue(array()));

		$cartSingletonFactory = new CartSingletonFactory($cartItemRepository);

		$sessionId = 'abc123';
		$customerIdentifier1 = new CustomerIdentifier($sessionId);
		$customerIdentifier2 = new CustomerIdentifier($sessionId);

		$cart1 = $cartSingletonFactory->get($customerIdentifier1);
		$cart2 = $cartSingletonFactory->get($customerIdentifier2);

		$this->assertTrue($cart1 === $cart2);
	}

	public function testGetDifferentCart() {
		$cartItemRepository = $this->getMockBuilder(CartItemRepository::class)
			->setMethods(array('__construct', 'findAllByCustomerIdentifier'))
			->disableOriginalConstructor()
			->getMock();
		$cartItemRepository->expects($this->exactly(2))->method('findAllByCustomerIdentifier')->will($this->returnValue(array()));

		$cartSingletonFactory = new CartSingletonFactory($cartItemRepository);

		$sessionId1 = 'abc123';
		$sessionId2 = 'def456';
		$customerIdentifier1 = new CustomerIdentifier($sessionId1);
		$customerIdentifier2 = new CustomerIdentifier($sessionId2);

		$cart1 = $cartSingletonFactory->get($customerIdentifier1);
		$cart2 = $cartSingletonFactory->get($customerIdentifier2);

		$this->assertFalse($cart1 === $cart2);
	}
	
}
