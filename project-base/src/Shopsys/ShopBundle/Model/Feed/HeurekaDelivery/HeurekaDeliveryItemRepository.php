<?php

namespace Shopsys\ShopBundle\Model\Feed\HeurekaDelivery;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Model\Feed\FeedItemRepositoryInterface;
use Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItemFactory;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Shopsys\ShopBundle\Model\Product\ProductRepository;

class HeurekaDeliveryItemRepository implements FeedItemRepositoryInterface
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
     * @var \Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItemFactory
     */
    private $heurekaDeliveryItemFactory;

    public function __construct(
        ProductRepository $productRepository,
        PricingGroupSettingFacade $pricingGroupSettingFacade,
        HeurekaDeliveryItemFactory $heurekaDeliveryItemFactory
    ) {
        $this->productRepository = $productRepository;
        $this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
        $this->heurekaDeliveryItemFactory = $heurekaDeliveryItemFactory;
    }

    /**
     * @inheritdoc
     */
    public function getItems(DomainConfig $domainConfig, $seekItemId, $maxResults)
    {
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

        return $this->heurekaDeliveryItemFactory->createItems($products, $domainConfig);
    }
}
