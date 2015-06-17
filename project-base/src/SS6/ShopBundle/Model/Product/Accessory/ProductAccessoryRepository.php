<?php

namespace SS6\ShopBundle\Model\Product\Accessory;

use Doctrine\ORM\EntityManager;
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

	public function __construct(EntityManager $em, ProductRepository $productRepository) {
		$this->em = $em;
		$this->productRepository = $productRepository;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	public function getProductAccesoryRepository() {
		return $this->em->getRepository(ProductAccessory::class);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function getTop3ListableAccessories(Product $product, $domainId, PricingGroup $pricingGroup) {
		$productAccessories = $this->getAllByProduct($product);

		$accessoriesIds = [];
		foreach ($productAccessories as $productAccessory) {
			$accessoriesIds[] = $productAccessory->getAccessory()->getId();
		}

		$queryBuilder = $this->productRepository->getAllListableQueryBuilder($domainId, $pricingGroup);
		$queryBuilder->andWhere('p.id IN (:accessoriesIds)')
			->setParameter('accessoriesIds', $accessoriesIds)
			->setMaxResults(3);

		return $queryBuilder->getQuery()->getResult();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\Accessory\ProductAccessory
	 */
	public function getAllByProduct(Product $product) {
		return $this->getProductAccesoryRepository()->findBy(['product' => $product]);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Product\Product $accessory
	 * @return \SS6\ShopBundle\Model\Product\Accessory\ProductAccessory|null
	 */
	public function findByProductAndAccessory(Product $product, Product $accessory) {
		return $this->getProductAccesoryRepository()->findOneBy([
			'product' => $product,
			'accessory' => $accessory,
		]);
	}

}
