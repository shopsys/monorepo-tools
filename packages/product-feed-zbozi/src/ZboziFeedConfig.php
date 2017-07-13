<?php

namespace Shopsys\ProductFeed\ZboziBundle;

use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\FeedConfigInterface;
use Shopsys\ProductFeed\FeedItemCustomValuesProviderInterface;
use Shopsys\ProductFeed\FeedItemRepositoryInterface;
use Shopsys\ProductFeed\StandardFeedItemInterface;

class ZboziFeedConfig implements FeedConfigInterface
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
        return 'Zboží.cz';
    }

    /**
     * @return string
     */
    public function getFeedName()
    {
        return 'zbozi';
    }

    /**
     * @return string
     */
    public function getTemplateFilepath()
    {
        return '@ShopsysProductFeedZbozi/feed.xml.twig';
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
            $customValues = $allCustomValues[$item->getItemId()];

            if (!$customValues->getShowInZboziFeed()) {
                unset($items[$key]);
                continue;
            }

            if ($item instanceof StandardFeedItemInterface) {
                $item->setCustomValue('cpc', $customValues->getZboziCpc());
                $item->setCustomValue('cpc_search', $customValues->getZboziCpcSearch());
            }
        }

        return $items;
    }
}
