<?php

namespace SS6\ShopBundle\Model\Product;

use Doctrine\ORM\EntityManager;
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
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Product\Product|null
	 */
	public function findById($id) {
		return $this->getProductRepository()->find($id);
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
		return $this->getProductRepository()->findBy(array('visible' => true));
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
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getVisibleById($id) {
		$criteria = array('id' => $id, 'visible' => true);
		$product = $this->getProductRepository()->findOneBy($criteria);
		
		if ($product === null) {
			throw new \SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException($criteria);
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
}
