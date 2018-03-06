<?php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

use Doctrine\ORM\EntityManager;

class BrandRepository
{
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getBrandRepository()
    {
        return $this->em->getRepository(Brand::class);
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getBrandDomainRepository()
    {
        return $this->em->getRepository(BrandDomain::class);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomain[]
     */
    public function getBrandDomainsByBrand(Brand $brand)
    {
        return $this->getBrandDomainRepository()->findBy([
            'brand' => $brand,
        ]);
    }

    /**
     * @param int $brandId
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\Brand
     */
    public function getById($brandId)
    {
        $brand = $this->getBrandRepository()->find($brandId);

        if ($brand === null) {
            $message = 'Brand with ID ' . $brandId . ' not found.';
            throw new \Shopsys\FrameworkBundle\Model\Product\Brand\Exception\BrandNotFoundException($message);
        }

        return $brand;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\Brand[]
     */
    public function getAll()
    {
        return $this->getBrandRepository()->findBy([], ['name' => 'asc']);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomain[]
     */
    public function getBrandDomainsIndexedByDomain($brand)
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('bd')
            ->from(BrandDomain::class, 'bd', 'bd.domainId')
            ->where('bd.brand = :brand')->setParameter('brand', $brand)
            ->orderBy('bd.domainId', 'ASC');

        return $queryBuilder->getQuery()->execute();
    }
}
