<?php

namespace Shopsys\ShopBundle\Model\Advert;

use Doctrine\ORM\EntityManager;

class AdvertRepository
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getAdvertRepository()
    {
        return $this->em->getRepository(Advert::class);
    }

    /**
     * @param string $advertId
     * @return \Shopsys\ShopBundle\Model\Advert\Advert|null
     */
    public function findById($advertId)
    {
        return $this->getAdvertRepository()->find($advertId);
    }

    /**
     * @param string $positionName
     * @param int $domainId
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getAdvertByPositionQueryBuider($positionName, $domainId)
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(Advert::class, 'a')
            ->where('a.positionName = :positionName')->setParameter('positionName', $positionName)
            ->andWhere('a.hidden = FALSE')
            ->andWhere('a.domainId = :domainId')->setParameter('domainId', $domainId);
    }

    /**
     * @param string $positionName
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Advert\Advert|null
     */
    public function findRandomAdvertByPosition($positionName, $domainId)
    {
        $count = $this->getAdvertByPositionQueryBuider($positionName, $domainId)
            ->select('COUNT(a)')
            ->getQuery()->getSingleScalarResult();

        if ($count === 0) {
            return null;
        }

        return $this->getAdvertByPositionQueryBuider($positionName, $domainId)
            ->setFirstResult(rand(0, $count - 1))
            ->setMaxResults(1)
            ->getQuery()->getSingleResult();
    }

    /**
     * @param int $advertId
     * @return \Shopsys\ShopBundle\Model\Advert\Advert
     */
    public function getById($advertId)
    {
        $advert = $this->getAdvertRepository()->find($advertId);
        if ($advert === null) {
            $message = 'Advert with ID ' . $advertId . ' not found';
            throw new \Shopsys\ShopBundle\Model\Advert\Exception\AdvertNotFoundException($message);
        }
        return $advert;
    }
}
