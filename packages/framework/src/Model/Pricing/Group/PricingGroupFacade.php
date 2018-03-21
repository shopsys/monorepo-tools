<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Group;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Customer\UserRepository;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductCalculatedPriceRepository;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository;

class PricingGroupFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupRepository
     */
    private $pricingGroupRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler
     */
    private $productPriceRecalculationScheduler;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade
     */
    private $pricingGroupSettingFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository
     */
    private $productVisibilityRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductCalculatedPriceRepository
     */
    private $productCalculatedPriceRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\UserRepository
     */
    private $userRepository;

    public function __construct(
        EntityManagerInterface $em,
        PricingGroupRepository $pricingGroupRepository,
        Domain $domain,
        ProductPriceRecalculationScheduler $productPriceRecalculationScheduler,
        PricingGroupSettingFacade $pricingGroupSettingFacade,
        ProductVisibilityRepository $productVisibilityRepository,
        ProductCalculatedPriceRepository $productCalculatedPriceRepository,
        UserRepository $userRepository
    ) {
        $this->em = $em;
        $this->pricingGroupRepository = $pricingGroupRepository;
        $this->domain = $domain;
        $this->productPriceRecalculationScheduler = $productPriceRecalculationScheduler;
        $this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
        $this->productVisibilityRepository = $productVisibilityRepository;
        $this->productCalculatedPriceRepository = $productCalculatedPriceRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param int $pricingGroupId
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup
     */
    public function getById($pricingGroupId)
    {
        return $this->pricingGroupRepository->getById($pricingGroupId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData $pricingGroupData
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup
     */
    public function create(PricingGroupData $pricingGroupData, $domainId)
    {
        $pricingGroup = new PricingGroup($pricingGroupData, $domainId);

        $this->em->persist($pricingGroup);
        $this->em->flush();

        $this->productPriceRecalculationScheduler->scheduleAllProductsForDelayedRecalculation();
        $this->productVisibilityRepository->createAndRefreshProductVisibilitiesForPricingGroup(
            $pricingGroup,
            $pricingGroup->getDomainId()
        );
        $this->productCalculatedPriceRepository->createProductCalculatedPricesForPricingGroup($pricingGroup);

        return $pricingGroup;
    }

    /**
     * @param int $pricingGroupId
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData $pricingGroupData
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup
     */
    public function edit($pricingGroupId, PricingGroupData $pricingGroupData)
    {
        $pricingGroup = $this->pricingGroupRepository->getById($pricingGroupId);
        $pricingGroup->edit($pricingGroupData);

        $this->em->flush();

        $this->productPriceRecalculationScheduler->scheduleAllProductsForDelayedRecalculation();

        return $pricingGroup;
    }

    /**
     * @param int $oldPricingGroupId
     * @param int|null $newPricingGroupId
     */
    public function delete($oldPricingGroupId, $newPricingGroupId = null)
    {
        $oldPricingGroup = $this->pricingGroupRepository->getById($oldPricingGroupId);
        if ($newPricingGroupId !== null) {
            $newPricingGroup = $this->pricingGroupRepository->getById($newPricingGroupId);
            $this->userRepository->replaceUsersPricingGroup($oldPricingGroup, $newPricingGroup);
        } else {
            $newPricingGroup = null;
        }

        if ($newPricingGroup !== null && $this->pricingGroupSettingFacade->isPricingGroupDefaultOnSelectedDomain($oldPricingGroup)) {
            $this->pricingGroupSettingFacade->setDefaultPricingGroupForSelectedDomain($newPricingGroup);
        }

        $this->em->remove($oldPricingGroup);
        $this->em->flush();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup[]
     */
    public function getAll()
    {
        return $this->pricingGroupRepository->getAll();
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup[]
     */
    public function getByDomainId($domainId)
    {
        return $this->pricingGroupRepository->getPricingGroupsByDomainId($domainId);
    }

    /**
     * @param int $id
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup[]
     */
    public function getAllExceptIdByDomainId($id, $domainId)
    {
        return $this->pricingGroupRepository->getAllExceptIdByDomainId($id, $domainId);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup[][]
     */
    public function getAllIndexedByDomainId()
    {
        $pricingGroupsByDomainId = [];
        foreach ($this->domain->getAll() as $domain) {
            $domainId = $domain->getId();
            $pricingGroupsByDomainId[$domainId] = $this->pricingGroupRepository->getPricingGroupsByDomainId($domainId);
        }

        return $pricingGroupsByDomainId;
    }
}
