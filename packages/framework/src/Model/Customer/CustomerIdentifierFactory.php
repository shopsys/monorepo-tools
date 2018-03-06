<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

use Symfony\Component\HttpFoundation\Session\Session;

class CustomerIdentifierFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;

    public function __construct(CurrentCustomer $currentCustomer, Session $session)
    {
        $this->currentCustomer = $currentCustomer;
        $this->session = $session;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier
     */
    public function get()
    {
        $cartIdentifier = $this->session->getId();

        // when session is not started, returning empty string is behaviour of session_id()
        if ($cartIdentifier === '') {
            $this->session->start();
            $cartIdentifier = $this->session->getId();
        }

        $customerIdentifier = new CustomerIdentifier($cartIdentifier, $this->currentCustomer->findCurrentUser());

        return $customerIdentifier;
    }

    /**
     * @param string $cartIdentifier
     * @return \Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier
     */
    public function getOnlyWithCartIdentifier($cartIdentifier)
    {
        return new CustomerIdentifier($cartIdentifier, null);
    }
}
