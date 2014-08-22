<?php

namespace SS6\ShopBundle\Model\Product;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductRepository;
use SS6\ShopBundle\Model\Product\ProductVisibilityFacade;

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
	 * @var \SS6\ShopBundle\Model\Product\ProductVisibilityFacade
	 */
	private $productVisibilityFacade;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Product\ProductRepository $productRepository
	 */
	public function __construct(EntityManager $em, ProductRepository $productRepository,
			ProductVisibilityFacade $productVisibilityFacade) {
		$this->em = $em;
		$this->productRepository = $productRepository;
		$this->productVisibilityFacade = $productVisibilityFacade;
	}
	
	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductData $productData
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function create(ProductData $productData) {
		$product = new Product($productData);

		$this->em->persist($product);
		$this->em->flush();
		
		$this->productVisibilityFacade->refreshProductsVisibility();
		
		return $product;
	}
	
	/**
	 * @param int $productId
	 * @param \SS6\ShopBundle\Model\Product\ProductData $productData
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function edit($productId, ProductData $productData) {
		$product = $this->productRepository->getById($productId);
		$product->edit($productData);

		$this->em->flush();
		
		$this->productVisibilityFacade->refreshProductsVisibility();
		
		return $product;
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
