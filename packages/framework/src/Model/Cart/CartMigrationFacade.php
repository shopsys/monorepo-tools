<?php

namespace Shopsys\FrameworkBundle\Model\Cart;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactoryInterface;
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
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifierFactory
     */
    protected $customerIdentifierFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactoryInterface
     */
    protected $cartItemFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Cart\CartFacade
     */
    protected $cartFacade;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifierFactory $customerIdentifierFactory
     * @param \Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactoryInterface $cartItemFactory
     * @param \Shopsys\FrameworkBundle\Model\Cart\CartFacade $cartFacade
     */
    public function __construct(
        EntityManagerInterface $em,
        CustomerIdentifierFactory $customerIdentifierFactory,
        CartItemFactoryInterface $cartItemFactory,
        CartFacade $cartFacade
    ) {
        $this->em = $em;
        $this->customerIdentifierFactory = $customerIdentifierFactory;
        $this->cartItemFactory = $cartItemFactory;
        $this->cartFacade = $cartFacade;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Cart $cart
     */
    protected function mergeCurrentCartWithCart(Cart $cart)
    {
        $customerIdentifier = $this->customerIdentifierFactory->get();
        $currentCart = $this->cartFacade->getCartByCustomerIdentifierCreateIfNotExists($customerIdentifier);
        $currentCart->mergeWithCart($cart, $this->cartItemFactory);

        foreach ($currentCart->getItems() as $item) {
            $this->em->persist($item);
        }

        $this->cartFacade->deleteCart($cart);

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
            $cart = $this->cartFacade->findCartByCustomerIdentifier($previousCustomerIdentifier);

            if ($cart !== null) {
                $this->mergeCurrentCartWithCart($cart);
            }
        }
        $session->set(self::SESSION_PREVIOUS_CART_IDENTIFIER, $session->getId());
    }
}
