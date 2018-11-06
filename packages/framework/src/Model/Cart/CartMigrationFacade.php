<?php

namespace Shopsys\FrameworkBundle\Model\Cart;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifierFactory;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class CartMigrationFacade
{
    const SESSION_PREVIOUS_CART_IDENTIFIER = 'previous_id';

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Cart\CartService
     */
    protected $cartService;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Cart\CartFactory
     */
    protected $cartFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifierFactory
     */
    protected $customerIdentifierFactory;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Cart\CartService $cartService
     * @param \Shopsys\FrameworkBundle\Model\Cart\CartFactory $cartFactory
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifierFactory $customerIdentifierFactory
     */
    public function __construct(
        EntityManagerInterface $em,
        CartService $cartService,
        CartFactory $cartFactory,
        CustomerIdentifierFactory $customerIdentifierFactory
    ) {
        $this->em = $em;
        $this->cartService = $cartService;
        $this->cartFactory = $cartFactory;
        $this->customerIdentifierFactory = $customerIdentifierFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Cart $cart
     */
    protected function mergeCurrentCartWithCart(Cart $cart)
    {
        $customerIdentifier = $this->customerIdentifierFactory->get();
        $currentCart = $this->cartFactory->get($customerIdentifier);
        $this->cartService->mergeCarts($currentCart, $cart, $customerIdentifier);

        foreach ($cart->getItems() as $itemToRemove) {
            $this->em->remove($itemToRemove);
        }

        foreach ($currentCart->getItems() as $item) {
            $this->em->persist($item);
        }

        $this->em->flush();
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\FilterControllerEvent $filterControllerEvent
     */
    public function onKernelController(FilterControllerEvent $filterControllerEvent)
    {
        $session = $filterControllerEvent->getRequest()->getSession();

        $previousCartIdentifier = $session->get(self::SESSION_PREVIOUS_CART_IDENTIFIER);
        if (!empty($previousCartIdentifier) && $previousCartIdentifier !== $session->getId()) {
            $previousCustomerIdentifier = $this->customerIdentifierFactory->getOnlyWithCartIdentifier($previousCartIdentifier);
            $cart = $this->cartFactory->get($previousCustomerIdentifier);
            $this->mergeCurrentCartWithCart($cart);
        }
        $session->set(self::SESSION_PREVIOUS_CART_IDENTIFIER, $session->getId());
    }
}
