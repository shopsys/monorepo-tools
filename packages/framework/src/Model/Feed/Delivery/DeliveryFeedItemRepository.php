<?php

namespace Shopsys\FrameworkBundle\Model\Feed\Delivery;

use Shopsys\FrameworkBundle\Model\Feed\FeedItemRepositoryInterface;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;
use Shopsys\ProductFeed\DomainConfigInterface;

class DeliveryFeedItemRepository implements FeedItemRepositoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade
     */
    private $pricingGroupSettingFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\Delivery\DeliveryFeedItemFactory
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
        /* @var $domainConfig \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig */
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
