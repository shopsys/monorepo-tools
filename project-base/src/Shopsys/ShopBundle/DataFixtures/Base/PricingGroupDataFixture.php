<?php

namespace Shopsys\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupData;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupFacade;

class PricingGroupDataFixture extends AbstractReferenceFixture
{

    const ORDINARY_DOMAIN_1 = 'pricing_group_ordinary_domain_1';
    const PARTNER_DOMAIN_1 = 'pricing_group_partner_domain_1';
    const VIP_DOMAIN_1 = 'pricing_group_vip_domain_1';

    public function load(ObjectManager $manager) {
        $pricingGroupData = new PricingGroupData();

        $pricingGroupData->name = 'Obyčejný zákazník';
        $this->createPricingGroup($pricingGroupData, self::ORDINARY_DOMAIN_1);

        $pricingGroupData->name = 'Partner';
        $this->createPricingGroup($pricingGroupData, self::PARTNER_DOMAIN_1);

        $pricingGroupData->name = 'VIP zákazník';
        $this->createPricingGroup($pricingGroupData, self::VIP_DOMAIN_1);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupData $pricingGroupData
     * @param string $referenceName
     */
    private function createPricingGroup(
        PricingGroupData $pricingGroupData,
        $referenceName
    ) {
        $pricingGroupFacade = $this->get(PricingGroupFacade::class);
        /* @var $pricingGroupFacade \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupFacade */

        $pricingGroup = $pricingGroupFacade->create($pricingGroupData, Domain::FIRST_DOMAIN_ID);
        $this->addReference($referenceName, $pricingGroup);
    }
}
