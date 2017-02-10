<?php

namespace Shopsys\ShopBundle\Model\Product\Pricing;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductManualInputPrice;
use Shopsys\ShopBundle\Model\Product\Product;

class ProductManualInputPriceRepository
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
    private function getProductManualInputPriceRepository() {
        return $this->em->getRepository(ProductManualInputPrice::class);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return \Shopsys\ShopBundle\Model\Product\Pricing\ProductManualInputPrice[]
     */
    public function getByProduct(Product $product) {
        return $this->getProductManualInputPriceRepository()->findBy(['product' => $product]);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig[] $domainConfigs
     * @return \Shopsys\ShopBundle\Model\Product\Pricing\ProductManualInputPrice[]
     */
    public function getByProductAndDomainConfigs(Product $product, array $domainConfigs) {
        if (count($domainConfigs) === 0) {
            return [];
        }

        $domainIds = [];
        foreach ($domainConfigs as $domainConfig) {
            $domainIds[] = $domainConfig->getId();
        }

        $queryBuilder = $this->getProductManualInputPriceRepository()->createQueryBuilder('pmp')
            ->join('pmp.pricingGroup', 'pg')
            ->andWhere('pmp.product = :product')->setParameter('product', $product)
            ->andWhere('pg.domainId IN (:domainsIds)')->setParameter('domainsIds', $domainIds);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\ShopBundle\Model\Product\Pricing\ProductManualInputPrice|null
     */
    public function findByProductAndPricingGroup(Product $product, PricingGroup $pricingGroup) {
        return $this->getProductManualInputPriceRepository()->findOneBy([
            'product' => $product,
            'pricingGroup' => $pricingGroup,
        ]);
    }
}
