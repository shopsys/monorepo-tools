<?php

namespace Shopsys\ShopBundle\Model\Feed\Standard;

use Doctrine\ORM\Query\Expr\Join;
use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ShopBundle\Model\Feed\FeedItemRepositoryInterface;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductDomain;
use Shopsys\ShopBundle\Model\Product\ProductRepository;

class StandardFeedItemRepository implements FeedItemRepositoryInterface
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
     * @var \Shopsys\ShopBundle\Model\Feed\Standard\StandardFeedItemFactory
     */
    private $feedItemFactory;

    public function __construct(
        ProductRepository $productRepository,
        PricingGroupSettingFacade $pricingGroupSettingFacade,
        StandardFeedItemFactory $feedItemFactory
    ) {
        $this->productRepository = $productRepository;
        $this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
        $this->feedItemFactory = $feedItemFactory;
    }

    /**
     * @inheritdoc
     */
    public function getItems(DomainConfigInterface $domainConfig, $seekItemId, $maxResults)
    {
        /* @var $domainConfig \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig */
        $defaultPricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainConfig->getId());
        $queryBuilder = $this->productRepository->getAllVisibleQueryBuilder($domainConfig->getId(), $defaultPricingGroup);
        $this->productRepository->addTranslation($queryBuilder, $domainConfig->getLocale());
        $queryBuilder
            ->addSelect('v')->join('p.vat', 'v')
            ->addSelect('a')->join('p.calculatedAvailability', 'a')
            ->addSelect('b')->leftJoin('p.brand', 'b')
            ->andWhere('p.variantType != :variantTypeMain')->setParameter('variantTypeMain', Product::VARIANT_TYPE_MAIN)
            ->join(ProductDomain::class, 'pd', Join::WITH, 'pd.product = p.id AND pd.domainId = :domainId')
            ->orderBy('p.id', 'asc')
            ->setMaxResults($maxResults);

        if ($seekItemId !== null) {
            $queryBuilder->andWhere('p.id > :seekItemId')->setParameter('seekItemId', $seekItemId);
        }

        $products = $queryBuilder->getQuery()->execute();

        return $this->feedItemFactory->createItems($products, $domainConfig);
    }
}
