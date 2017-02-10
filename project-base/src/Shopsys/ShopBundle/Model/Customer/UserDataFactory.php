<?php

namespace Shopsys\ShopBundle\Model\Customer;

use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;

class UserDataFactory {

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
     */
    private $pricingGroupSettingFacade;

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade $pricingGroupSettingFacade
     */
    public function __construct(PricingGroupSettingFacade $pricingGroupSettingFacade) {
        $this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
    }

    /**
     * @param int $domainId
     */
    public function createDefault($domainId) {
        $userData = new UserData();
        $userData->pricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainId);

        return $userData;
    }
}
