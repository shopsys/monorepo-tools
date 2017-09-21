<?php

namespace Shopsys\ShopBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupData;

class PricingGroupDataFixture extends AbstractReferenceFixture
{
    const PRICING_GROUP_ORDINARY_DOMAIN_2 = 'pricing_group_ordinary_domain_2';
    const PRICING_GROUP_VIP_DOMAIN_2 = 'pricing_group_vip_domain_2';

    public function load(ObjectManager $manager)
    {
        $pricingGroupData = new PricingGroupData();

        $pricingGroupData->name = 'Obyčejný zákazník';
        $domainId = 2;
        $this->createPricingGroup($pricingGroupData, $domainId, self::PRICING_GROUP_ORDINARY_DOMAIN_2);

        $pricingGroupData->name = 'VIP zákazník';
        $domainId1 = 2;
        $this->createPricingGroup($pricingGroupData, $domainId1, self::PRICING_GROUP_VIP_DOMAIN_2);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupData $pricingGroupData
     * @param int $domainId
     * @param string $referenceName
     */
    private function createPricingGroup(
        PricingGroupData $pricingGroupData,
        $domainId,
        $referenceName
    ) {
        $pricingGroupFacade = $this->get('shopsys.shop.pricing.group.pricing_group_facade');
        /* @var $pricingGroupFacade \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupFacade */

        $pricingGroup = $pricingGroupFacade->create($pricingGroupData, $domainId);
        $this->addReference($referenceName, $pricingGroup);
    }
}
