<?php

namespace Shopsys\FrameworkBundle\Model\Product\Elasticsearch;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductVisibility;

class ElasticsearchProductRepository
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

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
     * @return QueryBuilder
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
}
