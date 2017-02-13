<?php

namespace Shopsys\ShopBundle\Tests\Database\Model\Cart;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\ShopBundle\Model\Cart\Cart;
use Shopsys\ShopBundle\Model\Cart\CartFacade;
use Shopsys\ShopBundle\Model\Cart\CartFactory;
use Shopsys\ShopBundle\Model\Cart\CartService;
use Shopsys\ShopBundle\Model\Cart\Item\CartItem;
use Shopsys\ShopBundle\Model\Cart\Watcher\CartWatcherFacade;
use Shopsys\ShopBundle\Model\Customer\CurrentCustomer;
use Shopsys\ShopBundle\Model\Customer\CustomerIdentifier;
use Shopsys\ShopBundle\Model\Customer\CustomerIdentifierFactory;
use Shopsys\ShopBundle\Model\Order\PromoCode\CurrentPromoCodeFacade;
use Shopsys\ShopBundle\Model\Product\ProductRepository;
use Shopsys\ShopBundle\Tests\Test\DatabaseTestCase;

class CartFacadeTest extends DatabaseTestCase
{
    public function testAddProductToCartAddsItemsOnlyToCurrentCart()
    {
        $customerIdentifier = new CustomerIdentifier('secretSessionHash');
        $anotherCustomerIdentifier = new CustomerIdentifier('anotherSecretSessionHash');
        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        $productId = $product->getId();
        $quantity = 10;

        $cartFacade = $this->createCartFacade($customerIdentifier);

        $cartFacade->addProductToCart($productId, $quantity);

        $cart = $this->getCartByCustomerIdentifier($customerIdentifier);
        $cartItems = $cart->getItems();
        $product = array_pop($cartItems)->getProduct();
        $this->assertSame($productId, $product->getId(), 'Add correct product');

        $anotherCart = $this->getCartByCustomerIdentifier($anotherCustomerIdentifier);
        $this->assertSame(0, $anotherCart->getItemsCount(), 'Add only in their own cart');
    }

    public function testCannotAddUnsellableProductToCart()
    {
        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '6');
        $productId = $product->getId();
        $quantity = 1;

        $customerIdentifier = new CustomerIdentifier('secretSessionHash');
        $cartFacade = $this->createCartFacade($customerIdentifier);

        $this->setExpectedException('\Shopsys\ShopBundle\Model\Product\Exception\ProductNotFoundException');
        $cartFacade->addProductToCart($productId, $quantity);

        $cart = $this->getCartByCustomerIdentifier($customerIdentifier);
        $cartItems = $cart->getItems();

        $this->assertEmpty($cartItems, 'Product add not suppressed');
    }

    public function testCanChangeCartItemsQuantities()
    {
        $product1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        $product2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '3');

        $customerIdentifier = new CustomerIdentifier('secretSessionHash');
        $cartFacade = $this->createCartFacade($customerIdentifier);

        $cartItem1 = $cartFacade->addProductToCart($product1->getId(), 1)->getCartItem();
        $cartItem2 = $cartFacade->addProductToCart($product2->getId(), 2)->getCartItem();

        $cartFacade->changeQuantities([
            $cartItem1->getId() => 5,
            $cartItem2->getId() => 9,
        ]);

        $cart = $this->getCartByCustomerIdentifier($customerIdentifier);
        foreach ($cart->getItems() as $cartItem) {
            if ($cartItem->getId() === $cartItem1->getId()) {
                $this->assertSame(5, $cartItem->getQuantity(), 'Correct change quantity product');
            } elseif ($cartItem->getId() === $cartItem2->getId()) {
                $this->assertSame(9, $cartItem->getQuantity(), 'Correct change quantity product');
            } else {
                $this->fail('Unexpected product in cart');
            }
        }
    }

    public function testCannotDeleteNonexistentCartItem()
    {
        $customerIdentifier = new CustomerIdentifier('secretSessionHash');

        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        $quantity = 1;

        $cartFacade = $this->createCartFacade($customerIdentifier);
        $cartFacade->addProductToCart($product->getId(), $quantity);

        $cart = $this->getCartByCustomerIdentifier($customerIdentifier);
        $cartItems = $cart->getItems();
        $cartItem = array_pop($cartItems);

        $this->setExpectedException('\Shopsys\ShopBundle\Model\Cart\Exception\InvalidCartItemException');
        $cartFacade->deleteCartItem($cartItem->getId() + 1);
    }

    public function testCanDeleteCartItem()
    {
        // Set currentLocale in TranslatableListener as it done in real request
        // because CartWatcherFacade works with entity translations.
        $translatableListener = $this->getContainer()->get(\Shopsys\ShopBundle\Model\Localization\TranslatableListener::class);
        /* @var $translatableListener \Shopsys\ShopBundle\Model\Localization\TranslatableListener */
        $translatableListener->setCurrentLocale('cs');

        $customerIdentifier = new CustomerIdentifier('secretSessionHash');

        $product1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        $product2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '2');
        $quantity = 1;

        $cartFacade = $this->createCartFacade($customerIdentifier);
        $cartItem1 = $cartFacade->addProductToCart($product1->getId(), $quantity)->getCartItem();
        $cartItem2 = $cartFacade->addProductToCart($product2->getId(), $quantity)->getCartItem();

        $cartFacade->deleteCartItem($cartItem1->getId());

        $cart = $this->getCartByCustomerIdentifier($customerIdentifier);
        $cartItems = $cart->getItems();

        $this->assertArrayHasSameElements([$cartItem2], $cartItems);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Customer\CustomerIdentifier $customerIdentifier
     * @return \Shopsys\ShopBundle\Model\Cart\CartFacade
     */
    private function createCartFacade(CustomerIdentifier $customerIdentifier)
    {
        return new CartFacade(
            $this->getEntityManager(),
            $this->getContainer()->get(CartService::class),
            $this->getContainer()->get(CartFactory::class),
            $this->getContainer()->get(ProductRepository::class),
            $this->getCustomerIdentifierFactoryMock($customerIdentifier),
            $this->getContainer()->get(Domain::class),
            $this->getContainer()->get(CurrentCustomer::class),
            $this->getContainer()->get(CurrentPromoCodeFacade::class)
        );
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Customer\CustomerIdentifier $customerIdentifier
     * @return \Shopsys\ShopBundle\Model\Cart\Cart
     */
    private function getCartByCustomerIdentifier(CustomerIdentifier $customerIdentifier)
    {
        $cartFactory = $this->getContainer()->get(CartFactory::class);

        return $cartFactory->get($customerIdentifier);
    }

    /**
     * @param array $expected
     * @param array $actual
     */
    private function assertArrayHasSameElements(array $expected, array $actual)
    {
        foreach ($expected as $expectedElement) {
            $key = array_search($expectedElement, $actual, true);

            if ($key === false) {
                $this->fail('Actual array does not contain expected element: ' . var_export($expectedElement, true));
            }

            unset($actual[$key]);
        }

        if (!empty($actual)) {
            $this->fail('Actual array contains extra elements: ' . var_export($actual, true));
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Customer\CustomerIdentifier $customerIdentifier
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getCustomerIdentifierFactoryMock(CustomerIdentifier $customerIdentifier)
    {
        $customerIdentifierFactoryMock = $this->getMockBuilder(CustomerIdentifierFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $customerIdentifierFactoryMock->method('get')->willReturn($customerIdentifier);

        return $customerIdentifierFactoryMock;
    }
}
