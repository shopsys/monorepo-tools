<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPriceRepository;
use SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPriceService;
use SS6\ShopBundle\Model\Product\Product;

class ProductManualInputPriceFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPriceRepository
	 */
	private $productManualInputPriceRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPriceService
	 */
	private $productManualInputPriceService;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade
	 */
	private $pricingGroupFacade;

	public function __construct(
		EntityManager $em,
		ProductManualInputPriceRepository $productManualInputPriceRepository,
		PricingGroupFacade $pricingGroupFacade,
		ProductManualInputPriceService $productManualInputPriceService
	) {
		$this->em = $em;
		$this->productManualInputPriceRepository = $productManualInputPriceRepository;
		$this->pricingGroupFacade = $pricingGroupFacade;
		$this->productManualInputPriceService = $productManualInputPriceService;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param string $inputPrice
	 */
	public function refresh(Product $product, PricingGroup $pricingGroup, $inputPrice) {
		$manualInputPrice = $this->productManualInputPriceRepository->findByProductAndPricingGroup($product, $pricingGroup);
		$refreshedProductManualInputPrice = $this->productManualInputPriceService->refresh(
			$product,
			$pricingGroup,
			$inputPrice,
			$manualInputPrice);
		$this->em->persist($refreshedProductManualInputPrice);
		$this->em->flush($refreshedProductManualInputPrice);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPrice[]
	 */
	public function getAllByProduct(Product $product) {
		return $this->productManualInputPriceRepository->getByProduct($product);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 */
	public function deleteByProduct(Product $product) {
		$manualInputPrices = $this->productManualInputPriceRepository->getByProduct($product);
		foreach ($manualInputPrices as $manualInputPrice) {
			$this->em->remove($manualInputPrice);
		}
		$this->em->flush($manualInputPrices);
	}

}
