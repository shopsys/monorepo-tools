<?php

namespace Shopsys\ShopBundle\Model\Feed\Heureka;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Model\Feed\FeedItemRepositoryInterface;
use Shopsys\ShopBundle\Model\Feed\Heureka\HeurekaItemFactory;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Shopsys\ShopBundle\Model\Product\ProductRepository;

class HeurekaItemRepository implements FeedItemRepositoryInterface
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
     * @var \Shopsys\ShopBundle\Model\Feed\Heureka\HeurekaItemFactory
     */
    private $heurekaItemFactory;

    public function __construct(
        ProductRepository $productRepository,
        PricingGroupSettingFacade $pricingGroupSettingFacade,
        HeurekaItemFactory $heurekaItemFactory
    ) {
        $this->productRepository = $productRepository;
        $this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
        $this->heurekaItemFactory = $heurekaItemFactory;
    }

    /**
     * @inheritdoc
     */
    public function getItems(DomainConfig $domainConfig, $seekItemId, $maxResults) {
        $defaultPricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainConfig->getId());
        $queryBuilder = $this->productRepository->getAllSellableQueryBuilder($domainConfig->getId(), $defaultPricingGroup);
        $this->productRepository->addTranslation($queryBuilder, $domainConfig->getLocale());
        $queryBuilder
            ->addSelect('v')->join('p.vat', 'v')
            ->addSelect('a')->join('p.calculatedAvailability', 'a')
            ->addSelect('b')->leftJoin('p.brand', 'b')
            ->orderBy('p.id', 'asc')
            ->setMaxResults($maxResults);

        if ($seekItemId !== null) {
            $queryBuilder->andWhere('p.id > :seekItemId')->setParameter('seekItemId', $seekItemId);
        }

        $products = $queryBuilder->getQuery()->execute();

        return $this->heurekaItemFactory->createItems($products, $domainConfig);
    }
}
