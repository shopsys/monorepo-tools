<?php

namespace SS6\ShopBundle\Model\Product\TopProduct;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory;
use SS6\ShopBundle\Model\Product\Product;
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
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\TopProduct\TopProduct[]
	 */
	public function getAll($domainId) {
		return $this->topProductRepository->getAll($domainId);
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
	 * @param $domainId
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 */
	public function saveTopProductsForDomain($domainId, array $products) {
		$oldTopProducts = $this->topProductRepository->getAll($domainId);
		foreach ($oldTopProducts as $oldTopProduct) {
			$this->em->remove($oldTopProduct);
		}
		$this->em->flush($oldTopProducts);

		$topProducts = [];
		foreach ($products as $product) {
			$topProduct = new TopProduct($product, $domainId);
			$this->em->persist($topProduct);
			$topProducts[] = $topProduct;
		}
		$this->em->flush($topProducts);
	}

}
