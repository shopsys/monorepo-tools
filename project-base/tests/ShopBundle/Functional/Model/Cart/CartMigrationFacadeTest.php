<?php

namespace Tests\ShopBundle\Functional\Model\Cart;

use Doctrine\ORM\EntityManager;
use ReflectionClass;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Cart\Cart;
use Shopsys\FrameworkBundle\Model\Cart\CartFacade;
use Shopsys\FrameworkBundle\Model\Cart\CartMigrationFacade;
use Shopsys\FrameworkBundle\Model\Cart\Item\CartItem;
use Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactoryInterface;
use Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier;
use Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifierFactory;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\ShopBundle\Model\Product\Product;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class CartMigrationFacadeTest extends TransactionFunctionalTestCase
{
    public function testMergeWithCartReturnsCartWithSummedProducts()
    {
        $product1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . 1);
        $product2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . 2);

        // Cart merging is bound to Product Id
        $productReflectionClass = new ReflectionClass(Product::class);
        $idProperty = $productReflectionClass->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($product1, 1);
        $idProperty->setValue($product2, 2);

        $cartIdentifier1 = 'abc123';
        $cartIdentifier2 = 'def456';

        $customerIdentifier1 = new CustomerIdentifier($cartIdentifier1);
        $mainCart = new Cart($customerIdentifier1->getCartIdentifier());

        $customerIdentifier2 = new CustomerIdentifier($cartIdentifier2);
        $mergingCart = new Cart($customerIdentifier2->getCartIdentifier());

        $cartItem = new CartItem($mainCart, $product1, 2, Money::zero());
        $mainCart->addItem($cartItem);

        $cartItem1 = new CartItem($mergingCart, $product1, 3, Money::zero());
        $mergingCart->addItem($cartItem1);
        $cartItem2 = new CartItem($mergingCart, $product2, 1, Money::zero());
        $mergingCart->addItem($cartItem2);

        $entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->setMethods(['persist', 'flush'])
            ->disableOriginalConstructor()
            ->getMock();
        $customerIdentifierFactoryMock = $this->getMockBuilder(CustomerIdentifierFactory::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $customerIdentifierFactoryMock
            ->expects($this->any())->method('get')
            ->willReturn($customerIdentifier1);
        $cartItemFactory = $this->getContainer()->get(CartItemFactoryInterface::class);
        $cartFacadeMock = $this->getMockBuilder(CartFacade::class)
            ->setMethods(['getCartByCustomerIdentifierCreateIfNotExists', 'deleteCart'])
            ->disableOriginalConstructor()
            ->getMock();
        $cartFacadeMock
            ->expects($this->once())->method('getCartByCustomerIdentifierCreateIfNotExists')
            ->willReturn($mainCart);
        $cartFacadeMock
            ->expects($this->once())->method('deleteCart')
            ->with($this->equalTo($mergingCart));

        $cartMigrationFacade = new CartMigrationFacade(
            $entityManagerMock,
            $customerIdentifierFactoryMock,
            $cartItemFactory,
            $cartFacadeMock
        );

        $cartMigrationFacade->mergeCurrentCartWithCart($mergingCart);

        $this->assertSame(2, $mainCart->getItemsCount());

        $this->assertSame(5, $mainCart->getItems()[0]->getQuantity());
        $this->assertSame(1, $mainCart->getItems()[1]->getQuantity());
    }
}
