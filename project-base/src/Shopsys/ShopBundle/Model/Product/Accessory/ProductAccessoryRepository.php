<?php

namespace SS6\ShopBundle\Model\Product\Accessory;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Doctrine\QueryBuilderService;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Product\Accessory\ProductAccessory;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductRepository;

class ProductAccessoryRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Component\Doctrine\QueryBuilderService
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
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param int $limit
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function getTopOfferedAccessories(Product $product, $domainId, PricingGroup $pricingGroup, $limit) {
		$queryBuilder = $this->getAllOfferedAccessoriesByProductQueryBuilder($product, $domainId, $pricingGroup);
		$queryBuilder->setMaxResults($limit);

		return $queryBuilder->getQuery()->getResult();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\Accessory\ProductAccessory[]
	 */
	public function getAllByProduct(Product $product) {
		return $this->getProductAccessoryRepository()->findBy(['product' => $product], ['position' => 'asc']);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function getAllOfferedAccessoriesByProduct(Product $product, $domainId, PricingGroup $pricingGroup) {
		return $this->getAllOfferedAccessoriesByProductQueryBuilder($product, $domainId, $pricingGroup)
			->getQuery()
			->getResult();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Product\Product[]
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
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Product\Product $accessory
	 * @return \SS6\ShopBundle\Model\Product\Accessory\ProductAccessory|null
	 */
	public function findByProductAndAccessory(Product $product, Product $accessory) {
		return $this->getProductAccessoryRepository()->findOneBy([
			'product' => $product,
			'accessory' => $accessory,
		]);
	}

}
