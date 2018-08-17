<?php

namespace Tests\ShopBundle\Database\Model\Cart;

use DateTime;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Cart\CartFacade;
use Shopsys\FrameworkBundle\Model\Cart\CartFactory;
use Shopsys\FrameworkBundle\Model\Cart\CartService;
use Shopsys\FrameworkBundle\Model\Cart\Item\CartItemRepository;
use Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer;
use Shopsys\FrameworkBundle\Model\Customer\CustomerFacade;
use Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier;
use Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifierFactory;
use Shopsys\FrameworkBundle\Model\Order\PromoCode\CurrentPromoCodeFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;
use Shopsys\ShopBundle\Model\Product\Product;
use Tests\ShopBundle\Test\DatabaseTestCase;

class CartFacadeDeleteOldCartsTest extends DatabaseTestCase
{
    public function testOldUnregisteredCustomerCartGetsDeleted()
    {
        $product = $this->getProductById(1);

        $cartFacade = $this->getCartFacadeForUnregisteredCustomer();
        $this->addProductToCartAtTime($cartFacade, $product, new DateTime('- 61 days'));

        $cartFacade->deleteOldCarts();

        $this->assertCartItemCount($cartFacade, 0, 'Cart items should be deleted');
    }

    public function testUnregisteredCustomerCartDoesNotGetDeletedIfItContainsRecentlyAddedItem()
    {
        $product1 = $this->getProductById(1);
        $product2 = $this->getProductById(2);

        $cartFacade = $this->getCartFacadeForUnregisteredCustomer();
        $this->addProductToCartAtTime($cartFacade, $product1, new DateTime('- 59 days'));
        $this->addProductToCartAtTime($cartFacade, $product2, new DateTime('- 61 days'));

        $cartFacade->deleteOldCarts();

        $this->assertCartItemCount($cartFacade, 2, 'Cart items should not be deleted');
    }

    public function testOldRegisteredCustomerCartGetsDeleted()
    {
        $product = $this->getProductById(1);

        $cartFacade = $this->getCartFacadeForRegisteredCustomer();
        $this->addProductToCartAtTime($cartFacade, $product, new DateTime('- 121 days'));

        $cartFacade->deleteOldCarts();

        $this->assertCartItemCount($cartFacade, 0, 'Cart items should be deleted');
    }

    public function testRegisteredCustomerCartDoesNotGetDeletedIfItContainsRecentlyAddedItem()
    {
        $product1 = $this->getProductById(1);
        $product2 = $this->getProductById(2);

        $cartFacade = $this->getCartFacadeForRegisteredCustomer();
        $this->addProductToCartAtTime($cartFacade, $product1, new DateTime('- 119 days'));
        $this->addProductToCartAtTime($cartFacade, $product2, new DateTime('- 121 days'));

        $cartFacade->deleteOldCarts();

        $this->assertCartItemCount($cartFacade, 2, 'Cart items should not be deleted');
    }

    /**
     * @param int $productId
     * @return \Shopsys\ShopBundle\Model\Product\Product
     */
    private function getProductById($productId)
    {
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */

        return $productFacade->getById($productId);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Cart\CartFacade
     */
    private function getCartFacadeForRegisteredCustomer()
    {
        $customerFacade = $this->getContainer()->get(CustomerFacade::class);
        /* @var $customerFacade \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade */

        $user = $customerFacade->getUserById(1);

        return $this->getCartFacadeForCustomer(new CustomerIdentifier('', $user));
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Cart\CartFacade
     */
    private function getCartFacadeForUnregisteredCustomer()
    {
        return $this->getCartFacadeForCustomer(new CustomerIdentifier('randomString'));
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier $customerIdentifier
     * @return \Shopsys\FrameworkBundle\Model\Cart\CartFacade
     */
    private function getCartFacadeForCustomer(CustomerIdentifier $customerIdentifier)
    {
        return new CartFacade(
            $this->getEntityManager(),
            $this->getContainer()->get(CartService::class),
            $this->getContainer()->get(CartFactory::class),
            $this->getContainer()->get(ProductRepository::class),
            $this->getCustomerIdentifierFactoryMock($customerIdentifier),
            $this->getContainer()->get(Domain::class),
            $this->getContainer()->get(CurrentCustomer::class),
            $this->getContainer()->get(CurrentPromoCodeFacade::class),
            $this->getContainer()->get(CartItemRepository::class)
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier $customerIdentifier
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getCustomerIdentifierFactoryMock(CustomerIdentifier $customerIdentifier)
    {
        $customerIdentifierFactoryMock = $this->getMockBuilder(CustomerIdentifierFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $customerIdentifierFactoryMock->method('get')->willReturn($customerIdentifier);

        return $customerIdentifierFactoryMock;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\CartFacade $cartFacade
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param \DateTime $addedAt
     */
    private function addProductToCartAtTime(CartFacade $cartFacade, Product $product, DateTime $addedAt)
    {
        $cartItemResult = $cartFacade->addProductToCart($product->getId(), 1);

        $cartItemResult->getCartItem()->changeAddedAt($addedAt);

        $this->getEntityManager()->flush($cartItemResult->getCartItem());
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\CartFacade $cartFacade
     * @param int $count
     * @param string $message
     */
    private function assertCartItemCount(CartFacade $cartFacade, $count, $message)
    {
        $cartItems = $cartFacade->getCartOfCurrentCustomer()->getItems();
        $this->assertCount($count, $cartItems, $message);
    }
}
