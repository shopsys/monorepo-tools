<?php

namespace Shopsys\ShopBundle\Model\Pricing\Group;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;

class PricingGroupRepository
{
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
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getPricingGroupRepository() {
        return $this->em->getRepository(PricingGroup::class);
    }

    /**
     * @param int $pricingGroupId
     * @return \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup
     */
    public function getById($pricingGroupId) {
        $pricingGroup = $this->getPricingGroupRepository()->find($pricingGroupId);
        if ($pricingGroup === null) {
            $message = 'Pricing group with ID ' . $pricingGroupId . ' not found.';
            throw new \Shopsys\ShopBundle\Model\Pricing\Group\Exception\PricingGroupNotFoundException($message);
        }
        return $pricingGroup;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup[]
     */
    public function getAll() {
        return $this->getPricingGroupRepository()->findAll();
    }

    /**
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup[]
     */
    public function getPricingGroupsByDomainId($domainId) {
        return $this->getPricingGroupRepository()->findBy(['domainId' => $domainId]);
    }

    /**
     * @param int $pricingGroupId
     * @return \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup|null
     */
    public function findById($pricingGroupId) {
        return $this->getPricingGroupRepository()->find($pricingGroupId);
    }

    /**
     * @param int $pricingGroupId
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup[]
     */
    public function getAllExceptIdByDomainId($pricingGroupId, $domainId) {
        $qb = $this->getPricingGroupRepository()->createQueryBuilder('pg')
            ->where('pg.domainId = :domainId')
            ->andWhere('pg.id != :id')
            ->setParameters(['domainId' => $domainId, 'id' => $pricingGroupId]);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return bool
     */
    public function existsUserWithPricingGroup(PricingGroup $pricingGroup) {
        $query = $this->em->createQuery('
            SELECT COUNT(u)
            FROM ' . User::class . ' u
            WHERE u.pricingGroup = :pricingGroup')
            ->setParameter('pricingGroup', $pricingGroup);
        return $query->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR) > 0;
    }
}
