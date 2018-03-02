<?php

namespace Shopsys\FrameworkBundle\Model\Product\Filter;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Product\Flag\Flag;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;

class FlagFilterChoiceRepository
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    public function __construct(
        ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param string $locale
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @return \Shopsys\FrameworkBundle\Model\Product\Flag\Flag[]
     */
    public function getFlagFilterChoicesInCategory($domainId, PricingGroup $pricingGroup, $locale, Category $category)
    {
        $productsQueryBuilder = $this->productRepository->getListableInCategoryQueryBuilder(
            $domainId,
            $pricingGroup,
            $category
        );

        return $this->getVisibleFlagsByProductsQueryBuilder($productsQueryBuilder, $locale);
    }

    /**
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param string $locale
     * @param string|null $searchText
     * @return \Shopsys\FrameworkBundle\Model\Product\Flag\Flag[]
     */
    public function getFlagFilterChoicesForSearch($domainId, PricingGroup $pricingGroup, $locale, $searchText)
    {
        $productsQueryBuilder = $this->productRepository
            ->getListableBySearchTextQueryBuilder($domainId, $pricingGroup, $locale, $searchText);

        return $this->getVisibleFlagsByProductsQueryBuilder($productsQueryBuilder, $locale);
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
     * @param string $locale
     * @return \Shopsys\FrameworkBundle\Model\Product\Flag\Flag[]
     */
    private function getVisibleFlagsByProductsQueryBuilder(QueryBuilder $productsQueryBuilder, $locale)
    {
        $clonedProductsQueryBuilder = clone $productsQueryBuilder;

        $clonedProductsQueryBuilder
            ->select('1')
            ->join('p.flags', 'pf')
            ->andWhere('pf.id = f.id')
            ->andWhere('f.visible = true')
            ->resetDQLPart('orderBy');

        $flagsQueryBuilder = $productsQueryBuilder->getEntityManager()->createQueryBuilder();
        $flagsQueryBuilder
            ->select('f, ft')
            ->from(Flag::class, 'f')
            ->join('f.translations', 'ft', Join::WITH, 'ft.locale = :locale')
            ->andWhere($flagsQueryBuilder->expr()->exists($clonedProductsQueryBuilder))
            ->orderBy('ft.name', 'asc')
            ->setParameter('locale', $locale);

        foreach ($clonedProductsQueryBuilder->getParameters() as $parameter) {
            $flagsQueryBuilder->setParameter($parameter->getName(), $parameter->getValue());
        }

        return $flagsQueryBuilder->getQuery()->execute();
    }
}
