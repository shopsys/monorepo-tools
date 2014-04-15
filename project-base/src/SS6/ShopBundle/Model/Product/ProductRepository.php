<?php

namespace SS6\ShopBundle\Model\Product;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use SS6\ShopBundle\Model\Product\Product;

class ProductRepository {
	/** 
	 * @var EntityRepository
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
	 * @return SS6\ShopBundle\Model\Product\Product|null
	 */
	public function findById($id) {
		return $this->entityRepository->find($id);
	}
	
	/**
	 * @param int $id
	 * @return SS6\ShopBundle\Model\Product\Product|null
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
	 * @param int $id
	 * @return SS6\ShopBundle\Model\Product\Product
	 */
	public function getById($id) {
		$product = $this->findById($id);
		
		if ($product === null) {
			throw new Exception\ProductNotFoundException('Product with ID ' . $id . ' does not exist.');
		}
		
		return $product;
	}
}
