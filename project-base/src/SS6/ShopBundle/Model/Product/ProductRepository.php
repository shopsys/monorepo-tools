<?php

namespace SS6\ShopBundle\Model\Product;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Product;

class ProductRepository {
	
	/** 
	 * @var \Doctrine\ORM\EntityRepository
	 */
	private $entityRepository;

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		$this->entityRepository = $entityManager->getRepository(Product::class);
	}
	
	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Product\Product|null
	 */
	public function findById($id) {
		return $this->entityRepository->find($id);
	}
	
	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Product\Product|null
	 */
	public function findVisibleById($id) {
		$product = $this->findById($id);
		
		if ($product instanceof Product) {
			if (!$product->isVisible()) {
				$product = null;
			}
		}
		
		return $product;
	}
	
	/**
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function findAllVisible() {
		return $this->entityRepository->findBy(array('visible' => true));
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
	 * @return SS6\ShopBundle\Model\Product\Product
	 */
	public function getVisibleById($id) {
		$criteria = array('id' => $id, 'visible' => true);
		$product = $this->entityRepository->findOneBy($criteria);
		
		if ($product === null) {
			throw new Exception\ProductNotFoundException($criteria);
		}
		
		return $product;
	}
}
