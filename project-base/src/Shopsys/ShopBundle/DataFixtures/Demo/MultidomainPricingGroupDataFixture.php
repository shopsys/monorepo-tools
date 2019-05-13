<?php

declare(strict_types=1);

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade;

class MultidomainPricingGroupDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    public const PRICING_GROUP_ORDINARY_DOMAIN = 'pricing_group_ordinary_domain';
    public const PRICING_GROUP_VIP_DOMAIN = 'pricing_group_vip_domain';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade
     */
    protected $pricingGroupFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupDataFactoryInterface
     */
    protected $pricingGroupDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade $pricingGroupFacade
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupDataFactoryInterface $pricingGroupDataFactory
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(
        PricingGroupFacade $pricingGroupFacade,
        PricingGroupDataFactoryInterface $pricingGroupDataFactory,
        Domain $domain
    ) {
        $this->pricingGroupFacade = $pricingGroupFacade;
        $this->pricingGroupDataFactory = $pricingGroupDataFactory;
        $this->domain = $domain;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->domain->getAllIdsExcludingFirstDomain() as $domainId) {
            $this->loadForDomain($domainId);
        }
    }

    /**
     * @param int $domainId
     */
    protected function loadForDomain(int $domainId)
    {
        $pricingGroupData = $this->pricingGroupDataFactory->create();

        $pricingGroupData->name = 'Obyčejný zákazník';

        $alreadyCreatedDemoPricingGroupsByDomain = $this->pricingGroupFacade->getByDomainId($domainId);
        if (count($alreadyCreatedDemoPricingGroupsByDomain) > 0) {
            $pricingGroup = reset($alreadyCreatedDemoPricingGroupsByDomain);

            $this->pricingGroupFacade->edit($pricingGroup->getId(), $pricingGroupData);
            $this->addReferenceForDomain(self::PRICING_GROUP_ORDINARY_DOMAIN, $pricingGroup, $domainId);
        }

        $pricingGroupData->name = 'VIP zákazník';
        $this->createPricingGroup($pricingGroupData, $domainId, self::PRICING_GROUP_VIP_DOMAIN);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData $pricingGroupData
     * @param int $domainId
     * @param string $referenceName
     */
    protected function createPricingGroup(
        PricingGroupData $pricingGroupData,
        $domainId,
        $referenceName
    ) {
        $pricingGroup = $this->pricingGroupFacade->create($pricingGroupData, $domainId);
        $this->addReferenceForDomain($referenceName, $pricingGroup, $domainId);
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            PricingGroupDataFixture::class,
        ];
    }
}
