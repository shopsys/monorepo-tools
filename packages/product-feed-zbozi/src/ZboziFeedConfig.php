<?php

namespace Shopsys\ProductFeed\ZboziBundle;

use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\FeedConfigInterface;
use Shopsys\ProductFeed\StandardFeedItemInterface;
use Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainFacade;

class ZboziFeedConfig implements FeedConfigInterface
{
    /**
     * @var \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainFacade
     */
    private $zboziProductDomainFacade;

    /**
     * @param \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainFacade $zboziProductDomainFacade
     */
    public function __construct(
        ZboziProductDomainFacade $zboziProductDomainFacade
    ) {
        $this->zboziProductDomainFacade = $zboziProductDomainFacade;
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
        $sellableItems = array_filter($items, [$this, 'isItemSellable']);

        $productsIds = [];
        foreach ($sellableItems as $item) {
            $productsIds[] = $item->getId();
        }

        $zboziProductDomainsIndexedByProductId = $this->zboziProductDomainFacade->getZboziProductDomainsByProductsIdsDomainIdIndexedByProductId(
            $productsIds,
            $domainConfig->getId()
        );

        foreach ($sellableItems as $key => $item) {
            $show = true;
            $cpc = null;
            $cpcSearch = null;

            if (array_key_exists($item->getId(), $zboziProductDomainsIndexedByProductId)) {
                $show = $zboziProductDomainsIndexedByProductId[$item->getId()]->getShow();
                $cpc = $zboziProductDomainsIndexedByProductId[$item->getId()]->getCpc();
                $cpcSearch = $zboziProductDomainsIndexedByProductId[$item->getId()]->getCpcSearch();
            }

            if (!$show) {
                unset($sellableItems[$key]);
                continue;
            }

            $item->setCustomValue('cpc', $cpc);
            $item->setCustomValue('cpc_search', $cpcSearch);
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
}
