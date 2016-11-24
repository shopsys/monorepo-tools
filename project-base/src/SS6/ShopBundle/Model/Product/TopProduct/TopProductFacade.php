<?php

namespace SS6\ShopBundle\Model\Product\TopProduct;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory;
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
	 * @var \SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory
	 */
	private $productDetailFactory;

	public function __construct(
		EntityManager $em,
		TopProductRepository $topProductRepository,
		ProductDetailFactory $productDetailFactory
	) {
		$this->em = $em;
		$this->topProductRepository = $topProductRepository;
		$this->productDetailFactory = $productDetailFactory;
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
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Product\Detail\ProductDetail[]
	 */
	public function getAllOfferedProductDetails($domainId, $pricingGroup) {
		$products = $this->topProductRepository->getOfferedProductsForTopProductsOnDomain($domainId, $pricingGroup);
		return $this->productDetailFactory->getDetailsForProducts($products);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\TopProduct\TopProductData $topProductData
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\TopProduct\TopProduct
	 */
	public function create(TopProductData $topProductData, $domainId) {
		if ($this->alreadyExists($topProductData, $domainId)) {
			throw new \SS6\ShopBundle\Model\Product\TopProduct\Exception\TopProductAlreadyExistsException();
		}
		$topProduct = new TopProduct($domainId, $topProductData);
		$this->em->persist($topProduct);
		$this->em->flush($topProduct);

		return $topProduct;
	}

	/**
	 * @param int $id
	 * @param \SS6\ShopBundle\Model\Product\TopProduct\TopProductData $topProductData
	 * @return \SS6\ShopBundle\Model\Product\TopProduct\TopProduct
	 */
	public function edit($id, TopProductData $topProductData) {
		$topProduct = $this->topProductRepository->getById($id);
		if ($this->alreadyExists($topProductData, $topProduct->getDomainId())
			&& $topProduct->getProduct() !== $topProductData->product
		) {
			throw new \SS6\ShopBundle\Model\Product\TopProduct\Exception\TopProductAlreadyExistsException();
		}
		$topProduct->edit($topProductData);
		$this->em->flush($topProduct);

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
	 * @param int $domainId
	 * @return bool
	 */
	private function alreadyExists(TopProductData $topProductData, $domainId) {
		try {
			$this->topProductRepository->getByProductAndDomainId(
				$topProductData->product,
				$domainId
			);

			return true;
		} catch (\SS6\ShopBundle\Model\Product\TopProduct\Exception\TopProductNotFoundException $e) {
			return false;
		}
	}

}
