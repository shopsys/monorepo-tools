<?php

namespace Shopsys\ShopBundle\Model\Product\Accessory;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Doctrine\QueryBuilderService;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\ShopBundle\Model\Product\Accessory\ProductAccessory;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductRepository;

class ProductAccessoryRepository
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Doctrine\QueryBuilderService
     */
    private $queryBuilderService;

    public function __construct(
        EntityManager $em,
        ProductRepository $productRepository,
        QueryBuilderService $queryBuilderService
    ) {
        $this->em = $em;
        $this->productRepository = $productRepository;
        $this->queryBuilderService = $queryBuilderService;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getProductAccessoryRepository() {
        return $this->em->getRepository(ProductAccessory::class);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param int $limit
     * @return \Shopsys\ShopBundle\Model\Product\Product[]
     */
    public function getTopOfferedAccessories(Product $product, $domainId, PricingGroup $pricingGroup, $limit) {
        $queryBuilder = $this->getAllOfferedAccessoriesByProductQueryBuilder($product, $domainId, $pricingGroup);
        $queryBuilder->setMaxResults($limit);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return \Shopsys\ShopBundle\Model\Product\Accessory\ProductAccessory[]
     */
    public function getAllByProduct(Product $product) {
        return $this->getProductAccessoryRepository()->findBy(['product' => $product], ['position' => 'asc']);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\ShopBundle\Model\Product\Product[]
     */
    public function getAllOfferedAccessoriesByProduct(Product $product, $domainId, PricingGroup $pricingGroup) {
        return $this->getAllOfferedAccessoriesByProductQueryBuilder($product, $domainId, $pricingGroup)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\ShopBundle\Model\Product\Product[]
     */
    private function getAllOfferedAccessoriesByProductQueryBuilder(Product $product, $domainId, PricingGroup $pricingGroup) {
        $queryBuilder = $this->productRepository->getAllOfferedQueryBuilder($domainId, $pricingGroup);
        $this->queryBuilderService->addOrExtendJoin(
            $queryBuilder,
            ProductAccessory::class,
            'pa',
            'pa.accessory = p AND pa.product = :product'
        );
        $queryBuilder
            ->setParameter('product', $product)
            ->orderBy('pa.position', 'ASC');

        return $queryBuilder;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param \Shopsys\ShopBundle\Model\Product\Product $accessory
     * @return \Shopsys\ShopBundle\Model\Product\Accessory\ProductAccessory|null
     */
    public function findByProductAndAccessory(Product $product, Product $accessory) {
        return $this->getProductAccessoryRepository()->findOneBy([
            'product' => $product,
            'accessory' => $accessory,
        ]);
    }
}
