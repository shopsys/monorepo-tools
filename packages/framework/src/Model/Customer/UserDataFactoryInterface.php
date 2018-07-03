<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

interface UserDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Customer\UserData
     */
    public function create(): UserData;

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Customer\UserData
     */
    public function createForDomainId(int $domainId): UserData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @return \Shopsys\FrameworkBundle\Model\Customer\UserData
     */
    public function createFromUser(User $user): UserData;
}
