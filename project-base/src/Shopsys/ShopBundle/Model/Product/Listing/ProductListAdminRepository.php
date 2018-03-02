<?php

namespace Shopsys\FrameworkBundle\Model\Product\Listing;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Shopsys\FrameworkBundle\Component\String\DatabaseSearching;
use Shopsys\FrameworkBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use Shopsys\FrameworkBundle\Model\Localization\Localization;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPrice;
use Shopsys\FrameworkBundle\Model\Product\Product;

class ProductListAdminRepository
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Localization\Localization
     */
    private $localization;

    public function __construct(EntityManager $em, Localization $localization)
    {
        $this->em = $em;
        $this->localization = $localization;
    }

    /**
     * @param int $pricingGroupId
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getProductListQueryBuilder($pricingGroupId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('p, pt, COALESCE(pmip.inputPrice, p.price) AS priceForProductList')
            ->from(Product::class, 'p')
            ->leftJoin(
                ProductManualInputPrice::class,
                'pmip',
                Join::WITH,
                'pmip.product = p.id AND pmip.pricingGroup = :pricingGroupId'
            )
            ->leftJoin('p.translations', 'pt', Join::WITH, 'pt.locale = :locale')
            ->setParameters([
                'locale' => $this->localization->getAdminLocale(),
                'pricingGroupId' => $pricingGroupId,
            ]);

        return $queryBuilder;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \Shopsys\FrameworkBundle\Form\Admin\QuickSearch\QuickSearchFormData $quickSearchData
     */
    public function extendQueryBuilderByQuickSearchData(
        QueryBuilder $queryBuilder,
        QuickSearchFormData $quickSearchData
    ) {
        if ($quickSearchData->text !== null && $quickSearchData->text !== '') {
            $queryBuilder->andWhere('
                (
                    NORMALIZE(pt.name) LIKE NORMALIZE(:text)
                    OR
                    NORMALIZE(p.catnum) LIKE NORMALIZE(:text)
                    OR
                    NORMALIZE(p.partno) LIKE NORMALIZE(:text)
                )');
            $querySearchText = DatabaseSearching::getFullTextLikeSearchString($quickSearchData->text);
            $queryBuilder->setParameter('text', $querySearchText);
        }
    }
}
