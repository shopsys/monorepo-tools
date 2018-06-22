<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

class UserData
{
    /**
     * @var string|null
     */
    public $firstName;

    /**
     * @var string|null
     */
    public $lastName;

    /**
     * @var string|null
     */
    public $email;

    /**
     * @var string|null
     */
    public $password;

    /**
     * @var int
     */
    public $domainId;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup|null
     */
    public $pricingGroup;

    /**
     * @var \DateTime|null
     */
    public $createdAt;

    public function __construct()
    {
        $this->domainId = 1;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     */
    public function setFromEntity(User $user)
    {
        $this->domainId = $user->getDomainId();
        $this->firstName = $user->getFirstName();
        $this->lastName = $user->getLastName();
        $this->email = $user->getEmail();
        $this->pricingGroup = $user->getPricingGroup();
        $this->createdAt = $user->getCreatedAt();
    }
}
