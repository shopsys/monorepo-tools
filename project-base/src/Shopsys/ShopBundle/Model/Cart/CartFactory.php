<?php

namespace Shopsys\FrameworkBundle\Model\Cart;

use Shopsys\FrameworkBundle\Model\Cart\Item\CartItemRepository;
use Shopsys\FrameworkBundle\Model\Cart\Watcher\CartWatcherFacade;
use Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier;

class CartFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Cart\Cart[]
     */
    private $carts = [];

    /**
     * @var \Shopsys\FrameworkBundle\Model\Cart\Item\CartItemRepository
     */
    private $cartItemRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Cart\Watcher\CartWatcherFacade
     */
    private $cartWatcherFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Item\CartItemRepository $cartItemRepository
     * @param \Shopsys\FrameworkBundle\Model\Cart\Watcher\CartWatcherFacade $cartWatcherFacade
     */
    public function __construct(CartItemRepository $cartItemRepository, CartWatcherFacade $cartWatcherFacade)
    {
        $this->cartItemRepository = $cartItemRepository;
        $this->cartWatcherFacade = $cartWatcherFacade;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier $customerIdentifier
     * @return \Shopsys\FrameworkBundle\Model\Cart\Cart
     */
    public function get(CustomerIdentifier $customerIdentifier)
    {
        $customerIdentifierHash = $customerIdentifier->getObjectHash();
        if (!array_key_exists($customerIdentifierHash, $this->carts)) {
            $this->carts[$customerIdentifierHash] = $this->createNewCart($customerIdentifier);
        }

        $cart = $this->carts[$customerIdentifierHash];
        $this->cartWatcherFacade->checkCartModifications($cart);

        return $cart;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier $customerIdentifier
     * @return \Shopsys\FrameworkBundle\Model\Cart\Cart
     */
    private function createNewCart(CustomerIdentifier $customerIdentifier)
    {
        $cartItems = $this->cartItemRepository->getAllByCustomerIdentifier($customerIdentifier);

        return new Cart($cartItems);
    }

    public function clearCache()
    {
        $this->carts = [];
    }
}
