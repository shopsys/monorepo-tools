<?php

namespace SS6\ShopBundle\Model\Product;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductRepository;
use SS6\ShopBundle\Model\Product\ProductVisibilityRepository;

class ProductEditFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;
	
	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;
	
	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductVisibilityRepository
	 */
	private $productVisibilityRepository;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Product\ProductRepository $productRepository
	 */
	public function __construct(EntityManager $em, ProductRepository $productRepository,
			ProductVisibilityRepository $productVisibilityRepository) {
		$this->em = $em;
		$this->productRepository = $productRepository;
		$this->productVisibilityRepository = $productVisibilityRepository;
	}
	
	/**
	 * @param array $productData
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function create(array $productData) {
		$product = new Product($productData['name'],
			$productData['catnum'],
			$productData['partno'],
			$productData['ean'],
			$productData['description'],
			$productData['price'],
			$productData['sellingFrom'],
			$productData['sellingTo'],
			$productData['stockQuantity'],
			$productData['hidden']);

		$this->em->persist($product);
		$this->em->flush();
		
		$this->productVisibilityRepository->refreshProductsVisibility();
		
		return $this->productRepository->getById($product->getId());
	}
	
	/**
	 * @param int $productId
	 * @param array $productData
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function edit($productId, array $productData) {
		$product = $this->productRepository->getById($productId);
		$product->edit($productData['name'],
			$productData['catnum'],
			$productData['partno'],
			$productData['ean'],
			$productData['description'],
			$productData['price'],
			$productData['sellingFrom'],
			$productData['sellingTo'],
			$productData['stockQuantity'],
			$productData['hidden']);

		$this->em->persist($product);
		$this->em->flush();
		
		$this->productVisibilityRepository->refreshProductsVisibility();
		
		return $this->productRepository->getById($product->getId());
	}
	
	/**
	 * @param int $productId
	 */
	public function delete($productId) {
		$product = $this->productRepository->getById($productId);
		$this->em->remove($product);
		$this->em->flush();
	}
}
