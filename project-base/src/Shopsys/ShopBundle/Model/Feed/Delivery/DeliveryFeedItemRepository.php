<?php

namespace Shopsys\ShopBundle\Model\Feed\Delivery;

use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\FeedItemRepositoryInterface;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Shopsys\ShopBundle\Model\Product\ProductRepository;

class DeliveryFeedItemRepository implements FeedItemRepositoryInterface
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
     */
    private $pricingGroupSettingFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Feed\Delivery\DeliveryFeedItemFactory
     */
    private $deliveryFeedItemFactory;

    public function __construct(
        ProductRepository $productRepository,
        PricingGroupSettingFacade $pricingGroupSettingFacade,
        DeliveryFeedItemFactory $deliveryFeedItemFactory
    ) {
        $this->productRepository = $productRepository;
        $this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
        $this->deliveryFeedItemFactory = $deliveryFeedItemFactory;
    }

    /**
     * @inheritdoc
     */
    public function getItems(DomainConfigInterface $domainConfig, $seekItemId, $maxResults)
    {
        /* @var $domainConfig \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig */
        $defaultPricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainConfig->getId());
        $queryBuilder = $this->productRepository->getAllSellableUsingStockInStockQueryBuilder(
            $domainConfig->getId(),
            $defaultPricingGroup
        );
        $queryBuilder
            ->orderBy('p.id', 'asc')
            ->setMaxResults($maxResults);

        if ($seekItemId !== null) {
            $queryBuilder->andWhere('p.id > :seekItemId')->setParameter('seekItemId', $seekItemId);
        }

        $products = $queryBuilder->getQuery()->execute();

        return $this->deliveryFeedItemFactory->createItems($products);
    }
}
