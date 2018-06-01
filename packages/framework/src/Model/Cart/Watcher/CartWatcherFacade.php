<?php

namespace Shopsys\FrameworkBundle\Model\Cart\Watcher;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\FlashMessage\FlashMessageSender;
use Shopsys\FrameworkBundle\Model\Cart\Cart;
use Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer;

class CartWatcherFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Cart\Watcher\CartWatcherService
     */
    protected $cartWatcherService;

    /**
     * @var \Shopsys\FrameworkBundle\Component\FlashMessage\FlashMessageSender
     */
    protected $flashMessageSender;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer
     */
    protected $currentCustomer;

    public function __construct(
        FlashMessageSender $flashMessageSender,
        EntityManagerInterface $em,
        CartWatcherService $cartWatcherService,
        CurrentCustomer $currentCustomer
    ) {
        $this->flashMessageSender = $flashMessageSender;
        $this->em = $em;
        $this->cartWatcherService = $cartWatcherService;
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Cart $cart
     */
    public function checkCartModifications(Cart $cart)
    {
        $this->checkNotListableItems($cart);
        $this->checkModifiedPrices($cart);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Cart $cart
     */
    protected function checkModifiedPrices(Cart $cart)
    {
        $modifiedItems = $this->cartWatcherService->getModifiedPriceItemsAndUpdatePrices($cart);

        foreach ($modifiedItems as $cartItem) {
            $this->flashMessageSender->addInfoFlashTwig(
                t('Product <strong>{{ name }}</strong> you had in cart is no longer available. Please check your order.'),
                ['name' => $cartItem->getName()]
            );
        }

        if (count($modifiedItems) > 0) {
            $this->em->flush($modifiedItems);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Cart $cart
     */
    protected function checkNotListableItems(Cart $cart)
    {
        $notVisibleItems = $this->cartWatcherService->getNotListableItems($cart, $this->currentCustomer);

        $toFlush = [];
        foreach ($notVisibleItems as $cartItem) {
            try {
                $productName = $cartItem->getName();
                $this->flashMessageSender->addErrorFlashTwig(
                    t('The price of the product <strong>{{ name }}</strong> you have in cart has changed. Please, check your order.'),
                    ['name' => $productName]
                );
            } catch (\Shopsys\FrameworkBundle\Model\Product\Exception\ProductNotFoundException $e) {
                $this->flashMessageSender->addErrorFlash(
                    t('Product you had in cart is no longer in available. Please check your order.')
                );
            }

            $cart->removeItemById($cartItem->getId());
            $this->em->remove($cartItem);
            $toFlush[] = $cartItem;
        }

        if (count($toFlush) > 0) {
            $this->em->flush($toFlush);
        }
    }
}
