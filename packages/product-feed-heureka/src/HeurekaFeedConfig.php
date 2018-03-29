<?php

namespace Shopsys\ProductFeed\HeurekaBundle;

use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\FeedConfigInterface;
use Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade;
use Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainFacade;
use Shopsys\ProductFeed\StandardFeedItemInterface;

class HeurekaFeedConfig implements FeedConfigInterface
{

    /**
     * @var string[]
     */
    private $heurekaCategoryFullNamesCache = [];

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainFacade
     */
    private $heurekaProductDomainFacade;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade
     */
    private $heurekaCategoryFacade;

    /**
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainFacade $heurekaProductDomainFacade
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade $heurekaCategoryFacade
     */
    public function __construct(
        HeurekaProductDomainFacade $heurekaProductDomainFacade,
        HeurekaCategoryFacade $heurekaCategoryFacade
    ) {
        $this->heurekaProductDomainFacade = $heurekaProductDomainFacade;
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
        $sellableItems = array_filter($items, function ($item) {
            return $this->isItemSellable($item);
        });

        $productsIds = [];
        foreach ($sellableItems as $item) {
            $productsIds[] = $item->getId();
        }

        $heurekaProductDomainsIndexedByProductId = $this->heurekaProductDomainFacade->getHeurekaProductDomainsByProductsIdsDomainIdIndexedByProductId(
            $productsIds,
            $domainConfig->getId()
        );

        foreach ($sellableItems as $key => $item) {
            $cpc = array_key_exists($item->getId(), $heurekaProductDomainsIndexedByProductId) ?
                $heurekaProductDomainsIndexedByProductId[$item->getId()]->getCpc() : null;

            $item->setCustomValue('cpc', $cpc);

            $categoryName = $this->findHeurekaCategoryFullNameByCategoryIdUsingCache($item->getMainCategoryId());
            $item->setCustomValue('category_name', $categoryName);
        }

        return $sellableItems;
    }

    /**
     * @param \Shopsys\ProductFeed\StandardFeedItemInterface $item
     * @return bool
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
