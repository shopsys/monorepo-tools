<?php

namespace Shopsys\ShopBundle\Model\Feed\Zbozi;

use Doctrine\ORM\Query\Expr\Join;
use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Model\Feed\FeedItemRepositoryInterface;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Shopsys\ShopBundle\Model\Product\ProductDomain;
use Shopsys\ShopBundle\Model\Product\ProductRepository;

class ZboziItemRepository implements FeedItemRepositoryInterface
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
     * @var \Shopsys\ShopBundle\Model\Feed\Zbozi\ZboziItemFactory
     */
    private $zboziItemFactory;

    public function __construct(
        ProductRepository $productRepository,
        PricingGroupSettingFacade $pricingGroupSettingFacade,
        ZboziItemFactory $zboziItemFactory
    ) {
        $this->productRepository = $productRepository;
        $this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
        $this->zboziItemFactory = $zboziItemFactory;
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
            ->join(ProductDomain::class, 'pd', Join::WITH, 'pd.product = p.id AND pd.domainId = :domainId')
            ->andWhere('pd.showInZboziFeed = true')
            ->orderBy('p.id', 'asc')
            ->setMaxResults($maxResults);

        if ($seekItemId !== null) {
            $queryBuilder->andWhere('p.id > :seekItemId')->setParameter('seekItemId', $seekItemId);
        }

        $products = $queryBuilder->getQuery()->execute();

        return $this->zboziItemFactory->createItems($products, $domainConfig);
    }
}
