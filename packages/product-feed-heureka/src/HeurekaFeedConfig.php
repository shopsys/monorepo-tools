<?php

namespace Shopsys\ProductFeed\HeurekaBundle;

use Shopsys\Plugin\PluginDataStorageProviderInterface;
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
     * @var \Shopsys\Plugin\PluginDataStorageProviderInterface
     */
    private $pluginDataStorageProvider;

    public function __construct(
        HeurekaCategoryNameProviderInterface $heurekaCategoryNameProvider,
        PluginDataStorageProviderInterface $pluginDataStorageProvider
    ) {
        $this->heurekaCategoryNameProvider = $heurekaCategoryNameProvider;
        $this->pluginDataStorageProvider = $pluginDataStorageProvider;
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
     * @param \Shopsys\ProductFeed\FeedItemInterface[] $items
     * @param \Shopsys\ProductFeed\DomainConfigInterface $domainConfig
     * @return \Shopsys\ProductFeed\FeedItemInterface[]
     */
    public function processItems(array $items, DomainConfigInterface $domainConfig)
    {
        $productsDataById = $this->getProductsDataById($items);

        foreach ($items as $key => $item) {
            if ($item instanceof StandardFeedItemInterface) {
                $cpc = $productsDataById[$item->getId()]['cpc'][$domainConfig->getId()] ?? null;
                $item->setCustomValue('cpc', $cpc);

                $categoryName = $this->heurekaCategoryNameProvider->getHeurekaCategoryNameForItem($item, $domainConfig);
                $item->setCustomValue('category_name', $categoryName);
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
            ->getDataStorage(ShopsysProductFeedHeurekaBundle::class, 'product');

        return $productDataStorage->getMultiple($productIds);
    }
}
