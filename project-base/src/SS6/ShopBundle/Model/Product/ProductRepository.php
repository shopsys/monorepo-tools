<?php

namespace SS6\ShopBundle\Model\Product;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Product\Product;

class ProductRepository {
	
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
	private function getProductRepository() {
		return $this->em->getRepository(Product::class);
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getProductDomainRepository() {
		return $this->em->getRepository(ProductDomain::class);
	}

	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Product\Product|null
	 */
	public function findById($id) {
		return $this->getProductRepository()->find($id);
	}

	/**
	 * @param int $domainId
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	private function getAllVisibleByDomainIdQueryBuilder($domainId) {
		$qb = $this->em->createQueryBuilder()
			->select('p')
			->from(Product::class, 'p')
			->join(ProductDomain::class, 'pd', Join::WITH, 'pd.product = p.id')
			->where('pd.domainId = :domainId')
				->andWhere('pd.visible = TRUE')
			->orderBy('p.id');

		$qb->setParameter('domainId', $domainId);

		return $qb;
	}
	
	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function getAllVisibleByDomainId($domainId) {
		$qb = $this->getAllVisibleByDomainIdQueryBuilder($domainId);

		return $qb->getQuery()->getResult();
	}
	
	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Product\Product
	 * @throws \SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException
	 */
	public function getById($id) {
		$product = $this->findById($id);
		
		if ($product === null) {
			throw new \SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException('Product with ID ' . $id . ' does not exist.');
		}
		
		return $product;
	}
	
	/**
	 * @param int $id
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getVisibleByIdAndDomainId($id, $domainId) {
		$qb = $this->getAllVisibleByDomainIdQueryBuilder($domainId);
		$qb->andWhere('p.id = :productId');
		$qb->setParameter('productId', $id);

		$product = $qb->getQuery()->getOneOrNullResult();
		
		if ($product === null) {
			throw new \SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException($qb->getDQL());
		}
		
		return $product;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function getAllByVat(Vat $vat) {
		return $this->getProductRepository()->findBy(array('vat' => $vat));
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\ProductDomain[]
	 */
	public function getProductDomainsByProduct(Product $product) {
		return $this->getProductDomainRepository()->findBy(array(
			'product' => $product,
		));
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\ProductDomain|null
	 */
	public function findProductDomainByProductAndDomainId(Product $product, $domainId) {
		return $this->getProductDomainRepository()->findOneBy(array(
			'product' => $product,
			'domainId' => $domainId,
		));
	}

}
