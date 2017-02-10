<?php

namespace Shopsys\ShopBundle\Model\Product\Unit;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\Unit\Unit;

class UnitRepository
{

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getUnitRepository() {
        return $this->em->getRepository(Unit::class);
    }

    /**
     * @param int $unitId
     * @return \Shopsys\ShopBundle\Model\Product\Unit\Unit|null
     */
    public function findById($unitId) {
        return $this->getUnitRepository()->find($unitId);
    }

    /**
     * @param int $unitId
     * @return \Shopsys\ShopBundle\Model\Product\Unit\Unit
     */
    public function getById($unitId) {
        $unit = $this->findById($unitId);

        if ($unit === null) {
            throw new \Shopsys\ShopBundle\Model\Product\Unit\Exception\UnitNotFoundException('Unit with ID ' . $unitId . ' not found.');
        }

        return $unit;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getAllQueryBuilder() {
        return $this->em->createQueryBuilder()
            ->select('u')
            ->from(Unit::class, 'u')
            ->orderBy('u.id');
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Unit\Unit[]
     */
    public function getAll() {
        return $this->getAllQueryBuilder()->getQuery()->execute();
    }

    /**
     * @param int $unitId
     * @return \Shopsys\ShopBundle\Model\Product\Unit\Unit[]
     */
    public function getAllExceptId($unitId) {
        return $this->getAllQueryBuilder()
            ->where('u.id != :id')->setParameter('id', $unitId)
            ->getQuery()->execute();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Unit\Unit $unit
     * @return bool
     */
    public function existsProductWithUnit(Unit $unit) {
        $qb = $this->em->createQueryBuilder()
            ->select('COUNT(p)')
            ->from(Product::class, 'p')
            ->where('p.unit = :unit')->setParameter('unit', $unit);

        return $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR) > 0;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Unit\Unit $oldUnit
     * @param \Shopsys\ShopBundle\Model\Product\Unit\Unit $newUnit
     */
    public function replaceUnit(Unit $oldUnit, Unit $newUnit) {
        $this->em->createQueryBuilder()
            ->update(Product::class, 'p')
            ->set('p.unit', ':newUnit')->setParameter('newUnit', $newUnit)
            ->where('p.unit = :oldUnit')->setParameter('oldUnit', $oldUnit)
            ->getQuery()->execute();
    }
}
