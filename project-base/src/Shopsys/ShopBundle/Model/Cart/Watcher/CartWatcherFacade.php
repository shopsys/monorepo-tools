<?php

namespace Shopsys\ShopBundle\Model\Cart\Watcher;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\FlashMessage\FlashMessageSender;
use Shopsys\ShopBundle\Model\Cart\Cart;
use Shopsys\ShopBundle\Model\Cart\Watcher\CartWatcherService;
use Shopsys\ShopBundle\Model\Customer\CurrentCustomer;

class CartWatcherFacade {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Cart\Watcher\CartWatcherService
     */
    private $cartWatcherService;

    /**
     * @var \Shopsys\ShopBundle\Component\FlashMessage\FlashMessageSender
     */
    private $flashMessageSender;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\CurrentCustomer
     */
    private $currentCustomer;

    public function __construct(
        FlashMessageSender $flashMessageSender,
        EntityManager $em,
        CartWatcherService $cartWatcherService,
        CurrentCustomer $currentCustomer
    ) {
        $this->flashMessageSender = $flashMessageSender;
        $this->em = $em;
        $this->cartWatcherService = $cartWatcherService;
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Cart\Cart $cart
     */
    public function checkCartModifications(Cart $cart) {
        $this->checkNotListableItems($cart);
        $this->checkModifiedPrices($cart);

        $this->em->flush();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Cart\Cart $cart
     */
    private function checkModifiedPrices(Cart $cart) {
        $modifiedItems = $this->cartWatcherService->getModifiedPriceItemsAndUpdatePrices($cart);

        foreach ($modifiedItems as $cartItem) {
            $this->flashMessageSender->addInfoFlashTwig(
                t('Product <strong>{{ name }}</strong> you had in cart is no longer available. Please check your order.'),
                ['name' => $cartItem->getName()]
            );
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Cart\Cart $cart
     */
    private function checkNotListableItems(Cart $cart) {
        $notVisibleItems = $this->cartWatcherService->getNotListableItems($cart, $this->currentCustomer);

        foreach ($notVisibleItems as $cartItem) {
            try {
                $productName = $cartItem->getName();
                $this->flashMessageSender->addErrorFlashTwig(
                    t('The price of the product <strong>{{ name }}</strong> you have in cart has changed. Please, check your order.'),
                    ['name' => $productName]
                );
            } catch (\Shopsys\ShopBundle\Model\Product\Exception\ProductNotFoundException $e) {
                $this->flashMessageSender->addErrorFlash(
                    t('Product you had in cart is no longer in available. Please check your order.')
                );
            }

            $cart->removeItemById($cartItem->getId());
            $this->em->remove($cartItem);
        }

        $this->em->flush();
    }

}
