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
}
