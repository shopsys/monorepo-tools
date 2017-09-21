<?php

namespace Shopsys\ShopBundle\Model\Cart;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Cart\Item\CartItemRepository;
use Shopsys\ShopBundle\Model\Customer\CurrentCustomer;
use Shopsys\ShopBundle\Model\Customer\CustomerIdentifierFactory;
use Shopsys\ShopBundle\Model\Order\PromoCode\CurrentPromoCodeFacade;
use Shopsys\ShopBundle\Model\Product\ProductRepository;

class CartFacade
{
    const DAYS_LIMIT_FOR_UNREGISTERED = 60;
    const DAYS_LIMIT_FOR_REGISTERED = 120;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Cart\CartService
     */
    private $cartService;

    /**
     * @var \Shopsys\ShopBundle\Model\Cart\CartFactory
     */
    private $cartFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\CustomerIdentifierFactory
     */
    private $customerIdentifierFactory;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\PromoCode\CurrentPromoCodeFacade
     */
    private $currentPromoCodeFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Cart\Item\CartItemRepository
     */
    private $cartItemRepository;

    public function __construct(
        EntityManager $em,
        CartService $cartService,
        CartFactory $cartFactory,
        ProductRepository $productRepository,
        CustomerIdentifierFactory $customerIdentifierFactory,
        Domain $domain,
        CurrentCustomer $currentCustomer,
        CurrentPromoCodeFacade $currentPromoCodeFacade,
        CartItemRepository $cartItemRepository
    ) {
        $this->em = $em;
        $this->cartService = $cartService;
        $this->cartFactory = $cartFactory;
        $this->productRepository = $productRepository;
        $this->customerIdentifierFactory = $customerIdentifierFactory;
        $this->domain = $domain;
        $this->currentCustomer = $currentCustomer;
        $this->currentPromoCodeFacade = $currentPromoCodeFacade;
        $this->cartItemRepository = $cartItemRepository;
    }

    /**
     * @param int $productId
     * @param int $quantity
     * @return \Shopsys\ShopBundle\Model\Cart\AddProductResult
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
        $result = $this->cartService->addProductToCart($cart, $customerIdentifier, $product, $quantity);
        /* @var $result \Shopsys\ShopBundle\Model\Cart\AddProductResult */

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
        $this->cartService->changeQuantities($cart, $quantitiesByCartItemId);
        $this->em->flush();
    }

    /**
     * @param int $cartItemId
     */
    public function deleteCartItem($cartItemId)
    {
        $cart = $this->getCartOfCurrentCustomer();
        $cartItemToDelete = $this->cartService->getCartItemById($cart, $cartItemId);
        $cart->removeItemById($cartItemId);
        $this->em->remove($cartItemToDelete);
        $this->em->flush();
    }

    public function cleanCart()
    {
        $cart = $this->getCartOfCurrentCustomer();
        $cartItemsToDelete = $cart->getItems();
        $this->cartService->cleanCart($cart);

        foreach ($cartItemsToDelete as $cartItemToDelete) {
            $this->em->remove($cartItemToDelete);
        }

        $this->em->flush();

        $this->cleanAdditionalData();
    }

    /**
     * @param int $cartItemId
     * @return \Shopsys\ShopBundle\Model\Product\Product
     */
    public function getProductByCartItemId($cartItemId)
    {
        $cart = $this->getCartOfCurrentCustomer();

        return $this->cartService->getCartItemById($cart, $cartItemId)->getProduct();
    }

    public function cleanAdditionalData()
    {
        $this->currentPromoCodeFacade->removeEnteredPromoCode();
    }
    /**
     * @return \Shopsys\ShopBundle\Model\Cart\Cart
     */
    public function getCartOfCurrentCustomer()
    {
        $customerIdentifier = $this->customerIdentifierFactory->get();

        return $this->cartFactory->get($customerIdentifier);
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Order\Item\QuantifiedProduct[]
     */
    public function getQuantifiedProductsOfCurrentCustomerIndexedByCartItemId()
    {
        $cart = $this->getCartOfCurrentCustomer();

        return $this->cartService->getQuantifiedProductsIndexedByCartItemId($cart);
    }

    public function deleteOldCarts()
    {
        $this->cartItemRepository->deleteOldCartsForUnregisteredCustomers(self::DAYS_LIMIT_FOR_UNREGISTERED);
        $this->cartItemRepository->deleteOldCartsForRegisteredCustomers(self::DAYS_LIMIT_FOR_REGISTERED);
        $this->cartFactory->clearCache();
    }
}
