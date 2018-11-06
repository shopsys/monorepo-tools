<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

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

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade $pricingGroupFacade
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupDataFactoryInterface $pricingGroupDataFactory
     */
    public function __construct(
        PricingGroupFacade $pricingGroupFacade,
        PricingGroupDataFactoryInterface $pricingGroupDataFactory
    ) {
        $this->pricingGroupFacade = $pricingGroupFacade;
        $this->pricingGroupDataFactory = $pricingGroupDataFactory;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /**
         * The pricing group is created with specific ID in database migration.
         * @see \Shopsys\FrameworkBundle\Migrations\Version20180603135346
         */
        $defaultPricingGroup = $this->pricingGroupFacade->getById(1);
        /** @var $defaultPricingGroup \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup */
        $this->addReference(self::PRICING_GROUP_ORDINARY_DOMAIN_1, $defaultPricingGroup);

        $pricingGroupData = $this->pricingGroupDataFactory->create();
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
