<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductRepository;

class HeurekaFeedItemFacade
{
    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductRepository
     */
    protected $heurekaProductRepository;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem\HeurekaFeedItemFactory
     */
    protected $feedItemFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade
     */
    protected $pricingGroupSettingFacade;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem\HeurekaProductDataBatchLoader
     */
    protected $productDataBatchLoader;

    /**
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductRepository $heurekaProductRepository
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem\HeurekaFeedItemFactory $feedItemFactory
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade $pricingGroupSettingFacade
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem\HeurekaProductDataBatchLoader $productDataBatchLoader
     */
    public function __construct(
        HeurekaProductRepository $heurekaProductRepository,
        HeurekaFeedItemFactory $feedItemFactory,
        PricingGroupSettingFacade $pricingGroupSettingFacade,
        HeurekaProductDataBatchLoader $productDataBatchLoader
    ) {
        $this->heurekaProductRepository = $heurekaProductRepository;
        $this->feedItemFactory = $feedItemFactory;
        $this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
        $this->productDataBatchLoader = $productDataBatchLoader;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param int|null $lastSeekId
     * @param int $maxResults
     * @return \Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem\HeurekaFeedItem[]|iterable
     */
    public function getItems(DomainConfig $domainConfig, ?int $lastSeekId, int $maxResults): iterable
    {
        $pricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainConfig->getId());
        $products = $this->heurekaProductRepository->getProducts($domainConfig, $pricingGroup, $lastSeekId, $maxResults);
        $this->productDataBatchLoader->loadForProducts($products, $domainConfig);

        foreach ($products as $product) {
            yield $this->feedItemFactory->create($product, $domainConfig);
        }
    }
}
