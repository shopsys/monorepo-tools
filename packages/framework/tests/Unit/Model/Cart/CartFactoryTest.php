<?php

namespace Tests\FrameworkBundle\Unit\Model\Cart;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Model\Cart\CartFactory;
use Shopsys\FrameworkBundle\Model\Cart\Item\CartItemRepository;
use Shopsys\FrameworkBundle\Model\Cart\Watcher\CartWatcherFacade;
use Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier;

class CartFactoryTest extends TestCase
{
    public function testGetReturnsTheSameCartForTheSameCustomer()
    {
        $cartFactory = $this->getCartFactory();

        $cartIdentifier = 'abc123';
        $customerIdentifier1 = new CustomerIdentifier($cartIdentifier);
        $customerIdentifier2 = new CustomerIdentifier($cartIdentifier);

        $cart1 = $cartFactory->get($customerIdentifier1);
        $cart2 = $cartFactory->get($customerIdentifier2);

        $this->assertSame($cart1, $cart2, 'Users with the same session ID have different carts.');
    }

    public function testGetReturnsDifferentCartsForDifferentCustomers()
    {
        $cartFactory = $this->getCartFactory();

        $cartIdentifier1 = 'abc123';
        $cartIdentifier2 = 'def456';
        $customerIdentifier1 = new CustomerIdentifier($cartIdentifier1);
        $customerIdentifier2 = new CustomerIdentifier($cartIdentifier2);

        $cart1 = $cartFactory->get($customerIdentifier1);
        $cart2 = $cartFactory->get($customerIdentifier2);

        $this->assertNotSame($cart1, $cart2, 'Users with different session IDs have the same cart.');
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Cart\CartFactory
     */
    private function getCartFactory()
    {
        $cartItemRepository = $this->getMockBuilder(CartItemRepository::class)
            ->setMethods(['__construct', 'getAllByCustomerIdentifier'])
            ->disableOriginalConstructor()
            ->getMock();
        $cartItemRepository->expects($this->any())->method('getAllByCustomerIdentifier')->will($this->returnValue([]));

        $cartWatcherFacade = $this->getMockBuilder(CartWatcherFacade::class)
            ->setMethods(['__construct', 'checkCartModifications'])
            ->disableOriginalConstructor()
            ->getMock();
        $cartWatcherFacade->expects($this->any())->method('checkCartModifications');

        return new CartFactory($cartItemRepository, $cartWatcherFacade, new EntityNameResolver([]));
    }
}
