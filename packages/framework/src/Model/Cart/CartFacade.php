<?php

namespace Shopsys\FrameworkBundle\Model\Cart;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactoryInterface;
use Shopsys\FrameworkBundle\Model\Cart\Watcher\CartWatcherFacade;
use Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer;
use Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier;
use Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifierFactory;
use Shopsys\FrameworkBundle\Model\Order\PromoCode\CurrentPromoCodeFacade;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;

class CartFacade
{
    const DAYS_LIMIT_FOR_UNREGISTERED = 60;
    const DAYS_LIMIT_FOR_REGISTERED = 120;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Cart\CartFactory
     */
    protected $cartFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifierFactory
     */
    protected $customerIdentifierFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\PromoCode\CurrentPromoCodeFacade
     */
    protected $currentPromoCodeFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser
     */
    protected $productPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactoryInterface
     */
    protected $cartItemFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Cart\CartRepository
     */
    protected $cartRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Cart\Watcher\CartWatcherFacade
     */
    protected $cartWatcherFacade;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Cart\CartFactory $cartFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductRepository $productRepository
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifierFactory $customerIdentifierFactory
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer $currentCustomer
     * @param \Shopsys\FrameworkBundle\Model\Order\PromoCode\CurrentPromoCodeFacade $currentPromoCodeFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactoryInterface $cartItemFactory
     * @param \Shopsys\FrameworkBundle\Model\Cart\CartRepository $cartRepository
     * @param \Shopsys\FrameworkBundle\Model\Cart\Watcher\CartWatcherFacade $cartWatcherFacade
     */
    public function __construct(
        EntityManagerInterface $em,
        CartFactory $cartFactory,
        ProductRepository $productRepository,
        CustomerIdentifierFactory $customerIdentifierFactory,
        Domain $domain,
        CurrentCustomer $currentCustomer,
        CurrentPromoCodeFacade $currentPromoCodeFacade,
        ProductPriceCalculationForUser $productPriceCalculation,
        CartItemFactoryInterface $cartItemFactory,
        CartRepository $cartRepository,
        CartWatcherFacade $cartWatcherFacade
    ) {
        $this->em = $em;
        $this->cartFactory = $cartFactory;
        $this->productRepository = $productRepository;
        $this->customerIdentifierFactory = $customerIdentifierFactory;
        $this->domain = $domain;
        $this->currentCustomer = $currentCustomer;
        $this->currentPromoCodeFacade = $currentPromoCodeFacade;
        $this->productPriceCalculation = $productPriceCalculation;
        $this->cartItemFactory = $cartItemFactory;
        $this->cartRepository = $cartRepository;
        $this->cartWatcherFacade = $cartWatcherFacade;
    }

    /**
     * @param int $productId
     * @param int $quantity
     * @return \Shopsys\FrameworkBundle\Model\Cart\AddProductResult
     */
    public function addProductToCart($productId, $quantity)
    {
        $product = $this->productRepository->getSellableById(
            $productId,
            $this->domain->getId(),
            $this->currentCustomer->getPricingGroup()
        );
        $cart = $this->getCartOfCurrentCustomerCreateIfNotExists();

        /* @var $result \Shopsys\FrameworkBundle\Model\Cart\AddProductResult */
        $result = $cart->addProduct($product, $quantity, $this->productPriceCalculation, $this->cartItemFactory);

        $this->em->persist($result->getCartItem());
        $this->em->flush();

        return $result;
    }

    /**
     * @param array $quantitiesByCartItemId
     */
    public function changeQuantities(array $quantitiesByCartItemId)
    {
        $cart = $this->findCartOfCurrentCustomer();

        if ($cart === null) {
            throw new \Shopsys\FrameworkBundle\Model\Cart\Exception\CartIsEmptyException();
        }

        $cart->changeQuantities($quantitiesByCartItemId);
        $this->em->flush();
    }

    /**
     * @param int $cartItemId
     */
    public function deleteCartItem($cartItemId)
    {
        $cart = $this->findCartOfCurrentCustomer();

        if ($cart === null) {
            throw new \Shopsys\FrameworkBundle\Model\Cart\Exception\CartIsEmptyException();
        }

        $cartItemToDelete = $cart->getItemById($cartItemId);
        $cart->removeItemById($cartItemId);
        $this->em->remove($cartItemToDelete);
        $this->em->flush();

        if ($cart->isEmpty()) {
            $this->deleteCart($cart);
        }
    }

    public function deleteCartOfCurrentCustomer()
    {
        $cart = $this->findCartOfCurrentCustomer();

        if ($cart !== null) {
            $this->deleteCart($cart);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Cart $cart
     */
    public function deleteCart(Cart $cart)
    {
        $this->em->remove($cart);
        $this->em->flush();

        $this->cleanAdditionalData();
    }

    /**
     * @param int $cartItemId
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function getProductByCartItemId($cartItemId)
    {
        $cart = $this->findCartOfCurrentCustomer();

        if ($cart === null) {
            $message = 'CartItem with id = ' . $cartItemId . ' not found in cart.';
            throw new \Shopsys\FrameworkBundle\Model\Cart\Exception\InvalidCartItemException($message);
        }

        return $cart->getItemById($cartItemId)->getProduct();
    }

    public function cleanAdditionalData()
    {
        $this->currentPromoCodeFacade->removeEnteredPromoCode();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier $customerIdentifier
     * @return \Shopsys\FrameworkBundle\Model\Cart\Cart|null
     */
    public function findCartByCustomerIdentifier(CustomerIdentifier $customerIdentifier)
    {
        $cart = $this->cartRepository->findByCustomerIdentifier($customerIdentifier);

        if ($cart !== null) {
            $this->cartWatcherFacade->checkCartModifications($cart);
        }

        return $cart;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Cart\Cart|null
     */
    public function findCartOfCurrentCustomer()
    {
        $customerIdentifier = $this->customerIdentifierFactory->get();

        return $this->findCartByCustomerIdentifier($customerIdentifier);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Cart\Cart
     */
    public function getCartOfCurrentCustomerCreateIfNotExists()
    {
        $customerIdentifier = $this->customerIdentifierFactory->get();

        return $this->getCartByCustomerIdentifierCreateIfNotExists($customerIdentifier);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier $customerIdentifier
     * @return \Shopsys\FrameworkBundle\Model\Cart\Cart
     */
    public function getCartByCustomerIdentifierCreateIfNotExists(CustomerIdentifier $customerIdentifier)
    {
        $cart = $this->cartRepository->findByCustomerIdentifier($customerIdentifier);

        if ($cart === null) {
            $cart = $this->cartFactory->create($customerIdentifier);

            $this->em->persist($cart);
            $this->em->flush($cart);
        }

        return $cart;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedProduct[]
     */
    public function getQuantifiedProductsOfCurrentCustomerIndexedByCartItemId()
    {
        $cart = $this->findCartOfCurrentCustomer();

        if ($cart === null) {
            return [];
        }

        return $cart->getQuantifiedProductsIndexedByItemId();
    }

    public function deleteOldCarts()
    {
        $this->cartRepository->deleteOldCartsForUnregisteredCustomers(self::DAYS_LIMIT_FOR_UNREGISTERED);
        $this->cartRepository->deleteOldCartsForRegisteredCustomers(self::DAYS_LIMIT_FOR_REGISTERED);
    }
}
