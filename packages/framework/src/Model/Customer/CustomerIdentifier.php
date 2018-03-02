<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

class CustomerIdentifier
{
    /**
     * @var string
     */
    private $cartIdentifier = '';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\User|null
     */
    private $user;

    /**
     * @param string $cartIdentifier
     * @param \Shopsys\FrameworkBundle\Model\Customer\User|null $user
     */
    public function __construct($cartIdentifier, User $user = null)
    {
        if ($cartIdentifier === '' && $user === null) {
            $message = 'Can not be created empty CustomerIdentifier';
            throw new \Shopsys\FrameworkBundle\Model\Customer\Exception\EmptyCustomerIdentifierException($message);
        }

        $this->user = $user;
        if ($this->user === null) {
            $this->cartIdentifier = $cartIdentifier;
        }
    }

    /**
     * @return string
     */
    public function getCartIdentifier()
    {
        return $this->cartIdentifier;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Customer\User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getObjectHash()
    {
        if ($this->user instanceof User) {
            $userId = $this->user->getId();
        } else {
            $userId = 'NULL';
        }
        return 'session:' . $this->cartIdentifier . ';userId:' . $userId . ';';
    }
}
