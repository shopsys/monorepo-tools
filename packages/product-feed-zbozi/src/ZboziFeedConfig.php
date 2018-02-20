<?php

namespace Shopsys\ProductFeed\ZboziBundle;

use Shopsys\Plugin\PluginDataStorageProviderInterface;
use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\FeedConfigInterface;
use Shopsys\ProductFeed\StandardFeedItemInterface;

class ZboziFeedConfig implements FeedConfigInterface
{
    /**
     * @var \Shopsys\Plugin\PluginDataStorageProviderInterface
     */
    private $pluginDataStorageProvider;

    public function __construct(
        PluginDataStorageProviderInterface $pluginDataStorageProvider
    ) {
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
     * @return string|null
     */
    public function getAdditionalInformation()
    {
        return null;
    }

    /**
     * @param \Shopsys\ProductFeed\StandardFeedItemInterface[] $items
     * @param \Shopsys\ProductFeed\DomainConfigInterface $domainConfig
     * @return \Shopsys\ProductFeed\StandardFeedItemInterface[]
     */
    public function processItems(array $items, DomainConfigInterface $domainConfig)
    {
        $domainId = $domainConfig->getId();
        $sellableItems = array_filter($items, [$this, 'isItemSellable']);
        $productsDataById = $this->getProductsDataIndexedByItemId($sellableItems);

        foreach ($sellableItems as $key => $item) {
            $itemId = $item->getId();
            $productData = $productsDataById[$itemId] ?? [];
            $showInFeed = $productData['show'][$domainId] ?? true;

            if (!$showInFeed) {
                unset($sellableItems[$key]);
                continue;
            }

            $item->setCustomValue('cpc', $productData['cpc'][$domainId] ?? null);
            $item->setCustomValue('cpc_search', $productData['cpc_search'][$domainId] ?? null);
        }

        return $sellableItems;
    }

    /**
     * @param \Shopsys\ProductFeed\StandardFeedItemInterface[] $items
     * @return array
     */
    private function getProductsDataIndexedByItemId(array $items)
    {
        $productIds = [];
        foreach ($items as $item) {
            $productIds[] = $item->getId();
        }

        $productDataStorage = $this->pluginDataStorageProvider
            ->getDataStorage(ShopsysProductFeedZboziBundle::class, 'product');

        return $productDataStorage->getMultiple($productIds);
    }

    /**
     * @param \Shopsys\ProductFeed\StandardFeedItemInterface $item
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod) method is used through array_filter
     */
    private function isItemSellable(StandardFeedItemInterface $item)
    {
        return !$item->isSellingDenied();
    }
}
