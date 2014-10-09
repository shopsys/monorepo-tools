<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Product\ProductRepository;

class ProductFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductRepository $productRepository
	 */
	public function __construct(ProductRepository $productRepository) {
		$this->productRepository = $productRepository;
	}

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function getAllVisibleByDomainId($domainId) {
		return $this->productRepository->getAllVisibleByDomainId($domainId);
	}
	
}
