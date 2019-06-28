<?php

namespace Shopsys\FrameworkBundle\Model\Product\Search\Export;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Shopsys\FrameworkBundle\Component\Paginator\QueryPaginator;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductVisibility;

/**
 * @deprecated Use ProductSearchExportWithFilterRepository instead
 */
class ProductSearchExportRepository
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @param int $startFrom
     * @param int $batchSize
     * @return array
     */
    public function getProductsData(int $domainId, string $locale, int $startFrom, int $batchSize): array
    {
        $queryBuilder = $this->createQueryBuilder($domainId, $locale)
            ->setFirstResult($startFrom)
            ->setMaxResults($batchSize);

        $query = $queryBuilder->getQuery();
        return $query->getArrayResult();
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createQueryBuilder(int $domainId, string $locale): QueryBuilder
    {
        $fields = 'p.id, p.catnum, p.partno, p.ean, t.name, d.description, d.shortDescription';
        $queryBuilder = $this->em->createQueryBuilder()
            ->select($fields)
            ->from(Product::class, 'p')
                ->where('p.variantType != :variantTypeVariant')
            ->join(ProductVisibility::class, 'prv', Join::WITH, 'prv.product = p.id')
                ->andWhere('prv.domainId = :domainId')
                ->andWhere('prv.visible = TRUE')
            ->join('p.translations', 't')
                ->andWhere('t.locale = :locale')
            ->join('p.domains', 'd')
                ->andWhere('d.domainId = :domainId')
            ->groupBy($fields)
            ->orderBy('p.id');

        $queryBuilder->setParameter('domainId', $domainId)
            ->setParameter('locale', $locale)
            ->setParameter('variantTypeVariant', Product::VARIANT_TYPE_VARIANT);

        return $queryBuilder;
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @param int[] $productIds
     * @return array
     */
    public function getProductsDataForIds(int $domainId, string $locale, array $productIds): array
    {
        $queryBuilder = $this->createQueryBuilder($domainId, $locale)
            ->andWhere('p.id IN (:productIds)')
            ->setParameter('productIds', $productIds);

        $query = $queryBuilder->getQuery();
        return $query->getArrayResult();
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @return int
     */
    public function getProductTotalCountForDomainAndLocale(int $domainId, string $locale): int
    {
        $result = new QueryPaginator($this->createQueryBuilder($domainId, $locale));

        return $result->getTotalCount();
    }
}
