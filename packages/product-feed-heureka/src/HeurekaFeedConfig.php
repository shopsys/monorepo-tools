<?php

namespace Shopsys\ProductFeed\HeurekaBundle;

use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\FeedConfigInterface;
use Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade;
use Shopsys\ProductFeed\StandardFeedItemInterface;

class HeurekaFeedConfig implements FeedConfigInterface
{
    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\DataStorageProvider
     */
    private $dataStorageProvider;

    /**
     * @var string[]
     */
    private $heurekaCategoryFullNamesCache = [];

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade
     */
    private $heurekaCategoryFacade;

    /**
     * @param \Shopsys\ProductFeed\HeurekaBundle\DataStorageProvider $dataStorageProvider
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade $heurekaCategoryFacade
     */
    public function __construct(
        DataStorageProvider $dataStorageProvider,
        HeurekaCategoryFacade $heurekaCategoryFacade
    ) {
        $this->dataStorageProvider = $dataStorageProvider;
        $this->heurekaCategoryFacade = $heurekaCategoryFacade;
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
            $categoryName = $this->findHeurekaCategoryFullNameByCategoryIdUsingCache($item->getMainCategoryId());
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

    /**
     * @param int $categoryId
     * @return string|null
     */
    private function findHeurekaCategoryFullNameByCategoryIdUsingCache($categoryId)
    {
        if (!array_key_exists($categoryId, $this->heurekaCategoryFullNamesCache)) {
            $this->heurekaCategoryFullNamesCache[$categoryId] = $this->findHeurekaCategoryFullNameByCategoryId($categoryId);
        }
        return $this->heurekaCategoryFullNamesCache[$categoryId];
    }

    /**
     * @param int $categoryId
     * @return string|null
     */
    private function findHeurekaCategoryFullNameByCategoryId($categoryId)
    {
        $heurekaCategory = $this->heurekaCategoryFacade->findByCategoryId($categoryId);
        return $heurekaCategory !== null ? $heurekaCategory->getFullName() : null;
    }
}