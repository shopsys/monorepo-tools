<?php

namespace Shopsys\ProductFeed\HeurekaBundle;

use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\FeedConfigInterface;
use Shopsys\ProductFeed\HeurekaCategoryNameProviderInterface;
use Shopsys\ProductFeed\StandardFeedItemInterface;

class HeurekaFeedConfig implements FeedConfigInterface
{

    /**
     * @var \Shopsys\ProductFeed\HeurekaCategoryNameProviderInterface
     */
    private $heurekaCategoryNameProvider;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\DataStorageProvider
     */
    private $dataStorageProvider;

    public function __construct(
        HeurekaCategoryNameProviderInterface $heurekaCategoryNameProvider,
        DataStorageProvider $dataStorageProvider
    ) {
        $this->heurekaCategoryNameProvider = $heurekaCategoryNameProvider;
        $this->dataStorageProvider = $dataStorageProvider;
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
        $sellableItems = array_filter($items, [$this, 'isItemSellable']);
        $productsDataById = $this->getProductsDataIndexedByItemId($sellableItems);

        foreach ($sellableItems as $key => $item) {
            $cpc = $productsDataById[$item->getId()]['cpc'][$domainConfig->getId()] ?? null;
            $item->setCustomValue('cpc', $cpc);

            $categoryName = $this->heurekaCategoryNameProvider->getHeurekaCategoryNameForItem($item, $domainConfig);
            $item->setCustomValue('category_name', $categoryName);
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

        $productDataStorage = $this->dataStorageProvider->getProductDataStorage();

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
