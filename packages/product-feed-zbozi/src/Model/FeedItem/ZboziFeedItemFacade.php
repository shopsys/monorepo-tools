<?php

namespace Shopsys\ProductFeed\ZboziBundle\Model\FeedItem;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Shopsys\FrameworkBundle\Model\Product\Collection\ProductParametersBatchLoader;
use Shopsys\FrameworkBundle\Model\Product\Collection\ProductUrlsBatchLoader;
use Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainFacade;
use Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductRepository;

class ZboziFeedItemFacade
{
    /**
     * @var \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductRepository
     */
    protected $zboziProductRepository;

    /**
     * @var \Shopsys\ProductFeed\ZboziBundle\Model\FeedItem\ZboziFeedItemFactory
     */
    protected $feedItemFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade
     */
    protected $pricingGroupSettingFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Collection\ProductUrlsBatchLoader
     */
    protected $productUrlsBatchLoader;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Collection\ProductParametersBatchLoader
     */
    protected $productParametersBatchLoader;

    /**
     * @var \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainFacade
     */
    protected $zboziProductDomainFacade;

    /**
     * @param \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductRepository $zboziProductRepository
     * @param \Shopsys\ProductFeed\ZboziBundle\Model\FeedItem\ZboziFeedItemFactory $feedItemFactory
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade $pricingGroupSettingFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\Collection\ProductUrlsBatchLoader $productUrlsBatchLoader
     * @param \Shopsys\FrameworkBundle\Model\Product\Collection\ProductParametersBatchLoader $productParametersBatchLoader
     * @param \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainFacade $zboziProductDomainFacade
     */
    public function __construct(
        ZboziProductRepository $zboziProductRepository,
        ZboziFeedItemFactory $feedItemFactory,
        PricingGroupSettingFacade $pricingGroupSettingFacade,
        ProductUrlsBatchLoader $productUrlsBatchLoader,
        ProductParametersBatchLoader $productParametersBatchLoader,
        ZboziProductDomainFacade $zboziProductDomainFacade
    ) {
        $this->zboziProductRepository = $zboziProductRepository;
        $this->feedItemFactory = $feedItemFactory;
        $this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
        $this->productUrlsBatchLoader = $productUrlsBatchLoader;
        $this->productParametersBatchLoader = $productParametersBatchLoader;
        $this->zboziProductDomainFacade = $zboziProductDomainFacade;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param int|null $lastSeekId
     * @param int $maxResults
     * @return iterable
     */
    public function getItems(DomainConfig $domainConfig, ?int $lastSeekId, int $maxResults): iterable
    {
        $pricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainConfig->getId());
        $products = $this->zboziProductRepository->getProducts($domainConfig, $pricingGroup, $lastSeekId, $maxResults);
        $this->productUrlsBatchLoader->loadForProducts($products, $domainConfig);
        $this->productParametersBatchLoader->loadForProducts($products, $domainConfig);

        $zboziProductDomains = $this->zboziProductDomainFacade->getZboziProductDomainsByProductsAndDomainIndexedByProductId($products, $domainConfig);

        foreach ($products as $product) {
            $zboziProductDomain = $zboziProductDomains[$product->getId()] ?? null;

            yield $this->feedItemFactory->create($product, $zboziProductDomain, $domainConfig);
        }
    }
}
