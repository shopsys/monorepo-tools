<?php

namespace Shopsys\ShopBundle\Model\Customer;

use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CurrentCustomer
{
    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage
     */
    private $tokenStorage;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
     */
    private $pricingGroupSettingFacade;

    public function __construct(
        TokenStorage $tokenStorage,
        PricingGroupSettingFacade $ricingGroupSettingFacade
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->pricingGroupSettingFacade = $ricingGroupSettingFacade;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup
     */
    public function getPricingGroup() {
        $user = $this->findCurrentUser();
        if ($user === null) {
            return $this->pricingGroupSettingFacade->getDefaultPricingGroupByCurrentDomain();
        } else {
            return $user->getPricingGroup();
        }
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Customer\User|null
     */
    public function findCurrentUser() {
        $token = $this->tokenStorage->getToken();
        if ($token === null) {
            return null;
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            return null;
        }

        return $user;
    }
}
