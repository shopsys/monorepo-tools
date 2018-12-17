<?php

namespace Shopsys\FrameworkBundle\Model\Cart;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactoryInterface;
use Shopsys\FrameworkBundle\Model\Cart\Item\CartItemRepository;
use Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer;
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
     * @var \Shopsys\FrameworkBundle\Model\Cart\Item\CartItemRepository
     */
    protected $cartItemRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser
     */
    protected $productPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactoryInterface
     */
    protected $cartItemFactory;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Cart\CartFactory $cartFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductRepository $productRepository
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifierFactory $customerIdentifierFactory
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer $currentCustomer
     * @param \Shopsys\FrameworkBundle\Model\Order\PromoCode\CurrentPromoCodeFacade $currentPromoCodeFacade
     * @param \Shopsys\FrameworkBundle\Model\Cart\Item\CartItemRepository $cartItemRepository
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactoryInterface $cartItemFactory
     */
    public function __construct(
        EntityManagerInterface $em,
        CartFactory $cartFactory,
        ProductRepository $productRepository,
        CustomerIdentifierFactory $customerIdentifierFactory,
        Domain $domain,
        CurrentCustomer $currentCustomer,
        CurrentPromoCodeFacade $currentPromoCodeFacade,
        CartItemRepository $cartItemRepository,
        ProductPriceCalculationForUser $productPriceCalculation,
        CartItemFactoryInterface $cartItemFactory
    ) {
        $this->em = $em;
        $this->cartFactory = $cartFactory;
        $this->productRepository = $productRepository;
        $this->customerIdentifierFactory = $customerIdentifierFactory;
        $this->domain = $domain;
        $this->currentCustomer = $currentCustomer;
        $this->currentPromoCodeFacade = $currentPromoCodeFacade;
        $this->cartItemRepository = $cartItemRepository;
        $this->productPriceCalculation = $productPriceCalculation;
        $this->cartItemFactory = $cartItemFactory;
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
        $customerIdentifier = $this->customerIdentifierFactory->get();
        $cart = $this->cartFactory->get($customerIdentifier);
        $result = $cart->addProduct($customerIdentifier, $product, $quantity, $this->productPriceCalculation, $this->cartItemFactory);
        /* @var $result \Shopsys\FrameworkBundle\Model\Cart\AddProductResult */

        $this->em->persist($result->getCartItem());
        $this->em->flush();

        return $result;
    }

    /**
     * @param array $quantitiesByCartItemId
     */
    public function changeQuantities(array $quantitiesByCartItemId)
    {
        $cart = $this->getCartOfCurrentCustomer();
        $cart->changeQuantities($quantitiesByCartItemId);
        $this->em->flush();
    }

    /**
     * @param int $cartItemId
     */
    public function deleteCartItem($cartItemId)
    {
        $cart = $this->getCartOfCurrentCustomer();
        $cartItemToDelete = $cart->getCartItemById($cartItemId);
        $cart->removeItemById($cartItemId);
        $this->em->remove($cartItemToDelete);
        $this->em->flush();
    }

    public function cleanCart()
    {
        $cart = $this->getCartOfCurrentCustomer();
        $cartItemsToDelete = $cart->getItems();
        $cart->clean();

        foreach ($cartItemsToDelete as $cartItemToDelete) {
            $this->em->remove($cartItemToDelete);
        }

        $this->em->flush();

        $this->cleanAdditionalData();
    }

    /**
     * @param int $cartItemId
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function getProductByCartItemId($cartItemId)
    {
        $cart = $this->getCartOfCurrentCustomer();

        return $cart->getCartItemById($cartItemId)->getProduct();
    }

    public function cleanAdditionalData()
    {
        $this->currentPromoCodeFacade->removeEnteredPromoCode();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Cart\Cart
     */
    public function getCartOfCurrentCustomer()
    {
        $customerIdentifier = $this->customerIdentifierFactory->get();

        return $this->cartFactory->get($customerIdentifier);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedProduct[]
     */
    public function getQuantifiedProductsOfCurrentCustomerIndexedByCartItemId()
    {
        $cart = $this->getCartOfCurrentCustomer();

        return $cart->getQuantifiedProductsIndexedByCartItemId();
    }

    public function deleteOldCarts()
    {
        $this->cartItemRepository->deleteOldCartsForUnregisteredCustomers(self::DAYS_LIMIT_FOR_UNREGISTERED);
        $this->cartItemRepository->deleteOldCartsForRegisteredCustomers(self::DAYS_LIMIT_FOR_REGISTERED);
        $this->cartFactory->clearCache();
    }
}
