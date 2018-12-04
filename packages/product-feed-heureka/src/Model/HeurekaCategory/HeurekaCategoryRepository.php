<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory;

use Doctrine\ORM\EntityManagerInterface;

class HeurekaCategoryRepository
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
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getHeurekaCategoryRepository()
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
