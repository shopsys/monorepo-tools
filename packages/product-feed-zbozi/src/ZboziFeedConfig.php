<?php

namespace Shopsys\ProductFeed\ZboziBundle;

use Shopsys\Plugin\PluginDataStorageProviderInterface;
use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\FeedConfigInterface;
use Shopsys\ProductFeed\FeedItemRepositoryInterface;
use Shopsys\ProductFeed\StandardFeedItemInterface;

class ZboziFeedConfig implements FeedConfigInterface
{
    /**
     * @var \Shopsys\ProductFeed\FeedItemRepositoryInterface
     */
    private $feedItemRepository;

    /**
     * @var \Shopsys\Plugin\PluginDataStorageProviderInterface
     */
    private $pluginDataStorageProvider;

    public function __construct(
        FeedItemRepositoryInterface $feedItemRepository,
        PluginDataStorageProviderInterface $pluginDataStorageProvider
    ) {
        $this->feedItemRepository = $feedItemRepository;
        $this->pluginDataStorageProvider = $pluginDataStorageProvider;
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
        $domainId = $domainConfig->getId();
        $productsDataById = $this->getProductsDataById($items);

        foreach ($items as $key => $item) {
            $productData = $productsDataById[$item->getId()] ?? [];

            $showInFeed = $productData['show'][$domainId] ?? true;
            if (!$showInFeed) {
                unset($items[$key]);
                continue;
            }

            if ($item instanceof StandardFeedItemInterface) {
                $item->setCustomValue('cpc', $productData['cpc'][$domainId] ?? null);
                $item->setCustomValue('cpc_search', $productData['cpc_search'][$domainId] ?? null);
            }
        }

        return $items;
    }

    /**
     * @param array $items
     * @return array
     */
    private function getProductsDataById(array $items)
    {
        $productIds = [];
        foreach ($items as $item) {
            $productIds[] = $item->getId();
        }

        $productDataStorage = $this->pluginDataStorageProvider
            ->getDataStorage(ShopsysProductFeedZboziBundle::class, 'product');

        return $productDataStorage->getMultiple($productIds);
    }
}
