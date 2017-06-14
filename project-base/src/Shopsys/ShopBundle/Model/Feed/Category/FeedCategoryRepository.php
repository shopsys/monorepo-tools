<?php

namespace Shopsys\ShopBundle\Model\Feed\Category;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Feed\Category\FeedCategory;

class FeedCategoryRepository
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getFeedCategoryRepository()
    {
        return $this->em->getRepository(FeedCategory::class);
    }

    /**
     * @param int $extId
     * @return \Shopsys\ShopBundle\Model\Feed\Category\FeedCategory|null
     */
    public function findByExtId($extId)
    {
        return $this->getFeedCategoryRepository()->findOneBy([
            'extId' => $extId,
        ]);
    }

    /**
     * @param int[] $extIds
     */
    public function deleteAllExceptExtIds(array $extIds)
    {
        $qb = $this->em->createQueryBuilder();

        $qb->delete(FeedCategory::class, 'fc')
            ->where('fc.extId NOT IN (:currentExtIds)')
            ->setParameter('currentExtIds', $extIds);

        $qb->getQuery()->execute();
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Feed\Category\FeedCategory[]
     */
    public function getAllIndexedByExtId()
    {
        return $this->getFeedCategoryRepository()->createQueryBuilder('fc', 'fc.extId')->getQuery()->execute();
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Feed\Category\FeedCategory[]
     */
    public function getAllHeurekaCz()
    {
        return $this->getFeedCategoryRepository()->findBy([], ['fullName' => 'asc']);
    }
}
