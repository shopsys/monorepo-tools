<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use SS6\ShopBundle\Model\Product\Pricing\ProductInputPriceRepository;
use SS6\ShopBundle\Model\Product\Pricing\ProductInputPriceService;
use SS6\ShopBundle\Model\Product\Product;

class ProductInputPriceFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductInputPriceRepository
	 */
	private $productInputPriceRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductInputPriceService
	 */
	private $productInputPriceService;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade
	 */
	private $pricingGroupFacade;

	public function __construct(
		EntityManager $em,
		ProductInputPriceRepository $productInputPriceRepository,
		PricingGroupFacade $pricingGroupFacade,
		ProductInputPriceService $productInputPriceService
	) {
		$this->em = $em;
		$this->productInputPriceRepository = $productInputPriceRepository;
		$this->pricingGroupFacade = $pricingGroupFacade;
		$this->productInputPriceService = $productInputPriceService;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param string $inputPrice
	 */
	public function refresh(Product $product, PricingGroup $pricingGroup, $inputPrice) {
		$productInputPrice = $this->productInputPriceRepository->findByProductAndPricingGroup($product, $pricingGroup);
		$refreshedProductInputPrice = $this->productInputPriceService->refresh(
			$product,
			$pricingGroup,
			$inputPrice,
			$productInputPrice);
		$this->em->persist($refreshedProductInputPrice);
		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\Pricing\ProductInputPrice[]
	 */
	public function getAllByProduct(Product $product) {
		return $this->productInputPriceRepository->getByProduct($product);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 */
	public function deleteByProduct(Product $product) {
		$productInputPrices = $this->productInputPriceRepository->getByProduct($product);
		foreach ($productInputPrices as $productInputPrice) {
			$this->em->remove($productInputPrice);
		}
	}

}
