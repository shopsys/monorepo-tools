<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Product\Detail\Factory;
use SS6\ShopBundle\Model\Product\ProductRepository;

class ProductOnCurrentDomainFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Detail\Factory
	 */
	private $productDetailFactory;

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductRepository $productRepository
	 * @param \SS6\ShopBundle\Model\Domain\Domain $domain
	 * @param \SS6\ShopBundle\Model\Product\Detail\Factory $productDetailFactory
	 */
	public function __construct(ProductRepository $productRepository, Domain $domain, Factory $productDetailFactory) {
		$this->productRepository = $productRepository;
		$this->domain = $domain;
		$this->productDetailFactory = $productDetailFactory;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductListOrderingSetting $orderingSetting
	 * @return \SS6\ShopBundle\Model\Product\Detail\Detail[]
	 */
	public function getProductDetailsForProductList(ProductListOrderingSetting $orderingSetting) {
		$products = $this->productRepository->getProductsForProductList(
			$this->domain->getId(),
			$orderingSetting
		);

		return $this->productDetailFactory->getDetailsForProducts($products);
	}

	/**
	 * @param int $productId
	 * @return \SS6\ShopBundle\Model\Product\Detail\Detail
	 */
	public function getVisibleProductDetailById($productId) {
		$product = $this->productRepository->getVisibleByIdAndDomainId($productId, $this->domain->getId());

		return $this->productDetailFactory->getDetailForProduct($product);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductListOrderingSetting $orderingSetting
	 * @param int $page
	 * @param int $limit
	 * @return \SS6\ShopBundle\Model\Product\Detail\Detail[]
	 */
	public function getPaginatedProductDetailsForProductList(
		ProductListOrderingSetting $orderingSetting,
		$page,
		$limit
	) {
		$paginationResult = $this->productRepository->getPaginationResultForProductList(
			$this->domain->getId(),
			$orderingSetting,
			$page,
			$limit
		);
		$products = $paginationResult->getResults();

		return $this->productDetailFactory->getDetailsForProducts($products);

	}

}
