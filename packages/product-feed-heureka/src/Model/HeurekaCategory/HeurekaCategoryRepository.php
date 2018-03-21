<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Doctrine\QueryBuilderService;

class HeurekaCategoryRepository
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Doctrine\QueryBuilderService
     */
    private $queryBuilderService;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Component\Doctrine\QueryBuilderService $queryBuilderService
     */
    public function __construct(
        EntityManagerInterface $em,
        QueryBuilderService $queryBuilderService
    ) {
        $this->em = $em;
        $this->queryBuilderService = $queryBuilderService;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getHeurekaCategoryRepository()
    {
        return $this->em->getRepository(HeurekaCategory::class);
    }

    /**
     * @return \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategory[]
     */
    public function getAllIndexedById()
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('hc')
            ->from(HeurekaCategory::class, 'hc', 'hc.id');

        return $queryBuilder->getQuery()
            ->execute();
    }

    /**
     * @param int $categoryId
     * @return \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategory|null
     */
    public function findByCategoryId($categoryId)
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('hc')
            ->from(HeurekaCategory::class, 'hc')
            ->join('hc.categories', 'hcc')
            ->andWhere('hcc = :categoriesId')
            ->setParameter('categoriesId', $categoryId);

        return $queryBuilder->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param int $id
     * @return \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategory
     */
    public function getOneById($id)
    {
        $queryBuilder = $this->getHeurekaCategoryRepository()
            ->createQueryBuilder('hc')
            ->andWhere('hc.id = :id')
            ->setParameter('id', $id);
        $heurekaCategory = $queryBuilder->getQuery()->getOneOrNullResult();

        if ($heurekaCategory === null) {
            throw new HeurekaCategoryNotFoundException(
                'Heureka category with ID ' . $id . ' does not exist.'
            );
        }

        return $heurekaCategory;
    }
}
