<?php

namespace SS6\ShopBundle\Model\Product\TopProduct;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\TopProduct\TopProduct;

class TopProductRepository {

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
	private function getTopProductRepository() {
		return $this->em->getRepository(TopProduct::class);
	}

	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Product\TopProduct\TopProduct|null
	 */
	public function getById($id) {
		return $this->getOneByCriteria(array('id' => $id));
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\TopProduct\TopProduct|null
	 */
	public function getByProductAndDomainId(Product $product, $domainId) {
		return $this->getOneByCriteria(array('product' => $product, 'domainId' => $domainId));
	}

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\TopProduct\TopProduct[]
	 */
	public function getAll($domainId) {
		return $this->getTopProductRepository()->findBy(array('domainId' => $domainId));
	}

	/**
	 * @param array $criteria
	 * @return \SS6\ShopBundle\Model\Product\TopProduct\TopProduct|null
	 */
	private function getOneByCriteria(array $criteria) {
		$result = $this->getTopProductRepository()->findOneBy($criteria);
		if ($result === null) {
			throw new \SS6\ShopBundle\Model\Product\TopProduct\Exception\TopProductNotFoundException($criteria);
		}
		return $result;
	}

}
