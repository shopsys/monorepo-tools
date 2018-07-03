<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade;

class PricingGroupDataFixture extends AbstractReferenceFixture
{
    const PRICING_GROUP_ORDINARY_DOMAIN_1 = 'pricing_group_ordinary_domain_1';
    const PRICING_GROUP_PARTNER_DOMAIN_1 = 'pricing_group_partner_domain_1';
    const PRICING_GROUP_VIP_DOMAIN_1 = 'pricing_group_vip_domain_1';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade
     */
    private $pricingGroupFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupDataFactoryInterface
     */
    private $pricingGroupDataFactory;

    public function __construct(
        PricingGroupFacade $pricingGroupFacade,
        PricingGroupDataFactoryInterface $pricingGroupDataFactory
    ) {
        $this->pricingGroupFacade = $pricingGroupFacade;
        $this->pricingGroupDataFactory = $pricingGroupDataFactory;
    }

    public function load(ObjectManager $manager)
    {
        $pricingGroupData = $this->pricingGroupDataFactory->create();

        $pricingGroupData->name = 'Ordinary customer';
        $this->createPricingGroup($pricingGroupData, self::PRICING_GROUP_ORDINARY_DOMAIN_1);

        $pricingGroupData->name = 'Partner';
        $this->createPricingGroup($pricingGroupData, self::PRICING_GROUP_PARTNER_DOMAIN_1);

        $pricingGroupData->name = 'VIP customer';
        $this->createPricingGroup($pricingGroupData, self::PRICING_GROUP_VIP_DOMAIN_1);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData $pricingGroupData
     * @param string $referenceName
     */
    private function createPricingGroup(
        PricingGroupData $pricingGroupData,
        $referenceName
    ) {
        $pricingGroup = $this->pricingGroupFacade->create($pricingGroupData, Domain::FIRST_DOMAIN_ID);
        $this->addReference($referenceName, $pricingGroup);
    }
}
