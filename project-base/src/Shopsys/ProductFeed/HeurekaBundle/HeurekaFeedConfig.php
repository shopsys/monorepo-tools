<?php

namespace Shopsys\ProductFeed\HeurekaBundle;

use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\FeedConfigInterface;
use Shopsys\ProductFeed\FeedItemCustomValuesProviderInterface;
use Shopsys\ProductFeed\FeedItemRepositoryInterface;
use Shopsys\ProductFeed\StandardFeedItemInterface;

class HeurekaFeedConfig implements FeedConfigInterface
{
    /**
     * @var \Shopsys\ProductFeed\FeedItemRepositoryInterface
     */
    private $feedItemRepository;

    /**
     * @var \Shopsys\ProductFeed\FeedItemCustomValuesProviderInterface
     */
    private $feedItemCustomValuesProvider;

    public function __construct(
        FeedItemRepositoryInterface $feedItemRepository,
        FeedItemCustomValuesProviderInterface $feedItemCustomValuesProvider
    ) {
        $this->feedItemRepository = $feedItemRepository;
        $this->feedItemCustomValuesProvider = $feedItemCustomValuesProvider;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return 'Heureka';
    }

    /**
     * @return string
     */
    public function getFeedName()
    {
        return 'heureka';
    }

    /**
     * @return string
     */
    public function getTemplateFilepath()
    {
        return '@ShopsysProductFeedHeureka/feed.xml.twig';
    }

    /**
     * @return \Shopsys\ProductFeed\FeedItemRepositoryInterface
     */
    public function getFeedItemRepository()
    {
        return $this->feedItemRepository;
    }

    /**
     * @param \Shopsys\ProductFeed\FeedItemInterface[] $items
     * @param \Shopsys\ProductFeed\DomainConfigInterface $domainConfig
     * @return \Shopsys\ProductFeed\FeedItemInterface[]
     */
    public function processItems(array $items, DomainConfigInterface $domainConfig)
    {
        $allCustomValues = $this->feedItemCustomValuesProvider->getCustomValuesForItems($items, $domainConfig);

        foreach ($items as $key => $item) {
            if ($item instanceof StandardFeedItemInterface) {
                $customValues = $allCustomValues[$item->getItemId()];
                $item->setCustomValue('cpc', $customValues->getHeurekaCpc());

                $categoryName = $this->feedItemCustomValuesProvider->getHeurekaCategoryNameForItem($item, $domainConfig);
                $item->setCustomValue('category_name', $categoryName);
            }
        }

        return $items;
    }
}
