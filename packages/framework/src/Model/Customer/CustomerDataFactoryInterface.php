<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

interface CustomerDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Customer\CustomerData
     */
    public function create(): CustomerData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @return \Shopsys\FrameworkBundle\Model\Customer\CustomerData
     */
    public function createFromUser(User $user): CustomerData;
}
