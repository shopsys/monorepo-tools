<?php

namespace SS6\ShopBundle\Model\Product\TopProduct;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductRepository;
use SS6\ShopBundle\Model\Product\TopProduct\TopProduct;

class TopProductRepository {

	/**
	 * @var \Doctrine\ORM\EntityRepository
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager, ProductRepository $productRepository) {
		$this->em = $entityManager;
		$this->productRepository = $productRepository;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getTopProductRepository() {
		return $this->em->getRepository(TopProduct::class);
	}

	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Product\TopProduct\TopProduct
	 */
	public function getById($id) {
		$topProduct = $this->getTopProductRepository()->find($id);

		if ($topProduct === null) {
			throw new \SS6\ShopBundle\Model\Product\TopProduct\Exception\TopProductNotFoundException(
				'TopProduct with ID ' . $id . ' not found.'
			);
		}

		return $topProduct;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\TopProduct\TopProduct
	 */
	public function getByProductAndDomainId(Product $product, $domainId) {
		$topProduct = $this->getTopProductRepository()->findOneBy([
			'product' => $product,
			'domainId' => $domainId,
		]);

		if ($topProduct === null) {
			throw new \SS6\ShopBundle\Model\Product\TopProduct\Exception\TopProductNotFoundException();
		}
		return $topProduct;
	}

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\TopProduct\TopProduct[]
	 */
	public function getAll($domainId) {
		return $this->getTopProductRepository()->findBy(['domainId' => $domainId]);
	}

	/**
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function getOfferedProductsForTopProductsOnDomain($domainId, $pricingGroup) {
		$queryBuilder = $this->productRepository->getAllOfferedQueryBuilder($domainId, $pricingGroup);

		$queryBuilder
			->join(TopProduct::class, 'tp', Join::WITH, 'tp.product = p')
			->andWhere('tp.domainId = :domainId')
			->andWhere('tp.domainId = prv.domainId')
			->setParameter('domainId', $domainId);

		return $queryBuilder->getQuery()->execute();
	}
}
