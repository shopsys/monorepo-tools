<?php

namespace Shopsys\ProductFeed\HeurekaDeliveryBundle\Model\FeedItem;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade;

class HeurekaDeliveryFeedItemFacade
{
    /**
     * @var \Shopsys\ProductFeed\HeurekaDeliveryBundle\Model\FeedItem\HeurekaDeliveryDataRepository
     */
    protected $heurekaDeliveryDataRepository;

    /**
     * @var \Shopsys\ProductFeed\HeurekaDeliveryBundle\Model\FeedItem\HeurekaDeliveryFeedItemFactory
     */
    protected $feedItemFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade
     */
    protected $pricingGroupSettingFacade;

    public function __construct(
        HeurekaDeliveryDataRepository $heurekaDeliveryDataRepository,
        HeurekaDeliveryFeedItemFactory $feedItemFactory,
        PricingGroupSettingFacade $pricingGroupSettingFacade
    ) {
        $this->heurekaDeliveryDataRepository = $heurekaDeliveryDataRepository;
        $this->feedItemFactory = $feedItemFactory;
        $this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param int|null $lastSeekId
     * @param int $maxResults
     * @return \Shopsys\ProductFeed\HeurekaDeliveryBundle\Model\FeedItem\HeurekaDeliveryFeedItem[]|iterable
     */
    public function getItems(DomainConfig $domainConfig, ?int $lastSeekId, int $maxResults): iterable
    {
        $pricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainConfig->getId());
        $dataRows = $this->heurekaDeliveryDataRepository->getDataRows($domainConfig, $pricingGroup, $lastSeekId, $maxResults);

        foreach ($dataRows as $dataRow) {
            yield $this->feedItemFactory->create($dataRow);
        }
    }
}
