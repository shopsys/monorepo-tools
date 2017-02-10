<?php

namespace Shopsys\ShopBundle\Component\Router\FriendlyUrl;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl;
use Shopsys\ShopBundle\Model\Article\Article;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Product\Brand\Brand;
use Shopsys\ShopBundle\Model\Product\Product;

class FriendlyUrlToGenerateRepository {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    /**
     * @param string $routeName
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlData[]
     */
    public function getArticleData($routeName, DomainConfig $domainConfig) {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('a.id, a.name')
            ->distinct()
            ->from(Article::class, 'a')
            ->leftJoin(FriendlyUrl::class, 'f', Join::WITH, 'a.id = f.entityId AND f.routeName = :routeName AND f.domainId = a.domainId')
            ->setParameter('routeName', $routeName)
            ->where('f.entityId IS NULL AND a.domainId = :domainId')
            ->setParameter('domainId', $domainConfig->getId());

        return $this->createFriendlyUrlsData($queryBuilder);
    }

    /**
     * @param string $routeName
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlData[]
     */
    public function getProductDetailData($routeName, DomainConfig $domainConfig) {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('p.id, pt.name')
            ->distinct()
            ->from(Product::class, 'p')
            ->join('p.translations', 'pt', Join::WITH, 'pt.locale = :locale')
            ->setParameter('locale', $domainConfig->getLocale())
            ->leftJoin(FriendlyUrl::class, 'f', Join::WITH, 'p.id = f.entityId AND f.routeName = :routeName AND f.domainId = :domainId')
            ->setParameter('routeName', $routeName)
            ->setParameter('domainId', $domainConfig->getId())
            ->where('f.entityId IS NULL AND pt.name IS NOT NULL');

        return $this->createFriendlyUrlsData($queryBuilder);
    }

    /**
     * @param string $routeName
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlData[]
     */
    public function getProductListData($routeName, DomainConfig $domainConfig) {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('c.id, ct.name')
            ->distinct()
            ->from(Category::class, 'c')
            ->join('c.translations', 'ct', Join::WITH, 'ct.locale = :locale')
            ->setParameter('locale', $domainConfig->getLocale())
            ->leftJoin(FriendlyUrl::class, 'f', Join::WITH, 'c.id = f.entityId AND f.routeName = :routeName AND f.domainId = :domainId')
            ->setParameter('routeName', $routeName)
            ->setParameter('domainId', $domainConfig->getId())
            ->where('f.entityId IS NULL AND ct.name IS NOT NULL');

        return $this->createFriendlyUrlsData($queryBuilder);
    }

    /**
     * @param string $routeName
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return array
     */
    public function getBrandDetailData($routeName, DomainConfig $domainConfig) {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('b.id, b.name')
            ->distinct()
            ->from(Brand::class, 'b')
            ->leftJoin(FriendlyUrl::class, 'f', Join::WITH, 'b.id = f.entityId AND f.routeName = :routeName AND f.domainId = :domainId')
            ->setParameter('routeName', $routeName)
            ->setParameter('domainId', $domainConfig->getId())
            ->where('f.entityId IS NULL');

        return $this->createFriendlyUrlsData($queryBuilder);
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @return \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlData[]
     */
    private function createFriendlyUrlsData(QueryBuilder $queryBuilder) {
        $scalarData = $queryBuilder->getQuery()->getScalarResult();
        $friendlyUrlsData = [];

        foreach ($scalarData as $data) {
            $friendlyUrlData = new FriendlyUrlData();
            $friendlyUrlData->id = $data['id'];
            $friendlyUrlData->name = $data['name'];

            $friendlyUrlsData[] = $friendlyUrlData;
        }

        return $friendlyUrlsData;
    }

}
