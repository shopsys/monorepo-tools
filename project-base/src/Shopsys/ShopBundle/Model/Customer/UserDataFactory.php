<?php

namespace Shopsys\ShopBundle\Model\Customer;

use Shopsys\FrameworkBundle\Model\Customer\User as BaseUser;
use Shopsys\FrameworkBundle\Model\Customer\UserData as BaseUserData;
use Shopsys\FrameworkBundle\Model\Customer\UserDataFactory as BaseUserDataFactory;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade;

class UserDataFactory extends BaseUserDataFactory
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade $pricingGroupSettingFacade
     */
    public function __construct(PricingGroupSettingFacade $pricingGroupSettingFacade)
    {
        parent::__construct($pricingGroupSettingFacade);
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Customer\UserData
     */
    public function create(): BaseUserData
    {
        return new UserData();
    }

    /**
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Customer\UserData
     */
    public function createForDomainId(int $domainId): BaseUserData
    {
        $userData = new UserData();
        $this->fillForDomainId($userData, $domainId);

        return $userData;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Customer\User $user
     * @return \Shopsys\ShopBundle\Model\Customer\UserData
     */
    public function createFromUser(BaseUser $user): BaseUserData
    {
        $userData = new UserData();
        $this->fillFromUser($userData, $user);

        return $userData;
    }
}
