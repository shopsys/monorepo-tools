<?php

namespace SS6\ShopBundle\Model\Product\TopProduct;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory;
use SS6\ShopBundle\Model\Product\ProductRepository;
use SS6\ShopBundle\Model\Product\TopProduct\TopProductData;
use SS6\ShopBundle\Model\Product\TopProduct\TopProductRepository;

class TopProductFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\TopProduct\TopProductRepository
	 */
	private $topProductRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory
	 */
	private $productDetailFactory;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Product\TopProduct\TopProductRepository $topProductRepository
	 * @param \SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory $productDetailFactory
	 */
	public function __construct(
		EntityManager $em,
		TopProductRepository $topProductRepository,
		ProductRepository $productRepository,
		SelectedDomain $selectedDomain,
		ProductDetailFactory $productDetailFactory
	) {
		$this->em = $em;
		$this->topProductRepository = $topProductRepository;
		$this->productRepository = $productRepository;
		$this->selectedDomain = $selectedDomain;
		$this->productDetailFactory = $productDetailFactory;
	}

	/**
	 * @param int $productId
	 * @return \SS6\ShopBundle\Model\Product\TopProduct\TopProduct
	 */
	public function getByProductId($productId) {
		$product = $this->productRepository->findById($productId);
		return $this->topProductRepository->getByProductAndDomainId($product, $this->selectedDomain->getId());
	}

	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Product\TopProduct\TopProduct
	 */
	public function getById($id) {
		return $this->topProductRepository->getById($id);
	}

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\Detail\ProductDetail[]
	 */
	public function getAllProductDetailsByDomainId($domainId) {
		$products = $this->topProductRepository->getVisibleProductsForTopProductsOnDomain($domainId);
		return $this->productDetailFactory->getDetailsForProducts($products);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\TopProduct\TopProductData $topProductData
	 * @return \SS6\ShopBundle\Model\Product\TopProduct\TopProduct
	 */
	public function create(TopProductData $topProductData) {
		if ($this->alreadyExists($topProductData)) {
			throw new \SS6\ShopBundle\Model\Product\TopProduct\Exception\TopProductAlreadyExistsException();
		}
		$topProduct = new TopProduct($this->selectedDomain->getId(), $topProductData);
		$this->em->persist($topProduct);
		$this->em->flush();

		return $topProduct;
	}

	/**
	 * @param int $id
	 * @param \SS6\ShopBundle\Model\Product\TopProduct\TopProductData $topProductData
	 * @return \SS6\ShopBundle\Model\Product\TopProduct\TopProduct
	 */
	public function edit($id, TopProductData $topProductData) {
		$topProduct = $this->topProductRepository->getById($id);
		if ($this->alreadyExists($topProductData)
			&& $topProduct->getProduct() !== $topProductData->product
		) {
			throw new \SS6\ShopBundle\Model\Product\TopProduct\Exception\TopProductAlreadyExistsException();
		}
		$topProduct->edit($topProductData);
		$this->em->flush();

		return $topProduct;
	}

	/**
	 * @param int $id
	 */
	public function deleteById($id) {
		$topProduct = $this->topProductRepository->getById($id);

		$this->em->remove($topProduct);
		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\TopProduct\TopProductData $topProductData
	 * @return boolean
	 */
	private function alreadyExists(TopProductData $topProductData) {
		$exists = true;
		try {
			$this->topProductRepository->getByProductAndDomainId(
				$topProductData->product, $this->selectedDomain->getId()
			);
		} catch (\SS6\ShopBundle\Model\Product\TopProduct\Exception\TopProductNotFoundException $e) {
			$exists = false;
		}
		return (bool)$exists;
	}

}
