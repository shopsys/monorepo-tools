<?php

namespace Shopsys\FrameworkBundle\Model\Feed\Standard;

use Doctrine\ORM\Query\Expr\Join;
use Shopsys\FrameworkBundle\Model\Feed\FeedItemRepositoryInterface;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;
use Shopsys\ProductFeed\DomainConfigInterface;

class StandardFeedItemRepository implements FeedItemRepositoryInterface
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
     * @var \Shopsys\FrameworkBundle\Model\Feed\Standard\StandardFeedItemFactory
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
        /* @var $domainConfig \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig */
        $defaultPricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainConfig->getId());
        $queryBuilder = $this->productRepository->getAllVisibleQueryBuilder($domainConfig->getId(), $defaultPricingGroup);
        $this->productRepository->addTranslation($queryBuilder, $domainConfig->getLocale());
        $queryBuilder
            ->addSelect('v')->join('p.vat', 'v')
            ->addSelect('a')->join('p.calculatedAvailability', 'a')
            ->addSelect('b')->leftJoin('p.brand', 'b')
            ->addSelect('pd')->join('p.domains', 'pd', Join::WITH, 'pd.domainId = :domain')
            ->andWhere('p.variantType != :variantTypeMain')->setParameter('variantTypeMain', Product::VARIANT_TYPE_MAIN)
            ->orderBy('p.id', 'asc')
            ->setMaxResults($maxResults);

        if ($seekItemId !== null) {
            $queryBuilder->andWhere('p.id > :seekItemId')->setParameter('seekItemId', $seekItemId);
        }

        $products = $queryBuilder->getQuery()->execute();

        return $this->feedItemFactory->createItems($products, $domainConfig);
    }
}
