<?php

namespace SS6\ShopBundle\Model\Product;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Image\ImageFacade;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductEditDataFactory;
use SS6\ShopBundle\Model\Product\ProductEditFacade;
use SS6\ShopBundle\Model\Product\ProductVariantService;

class ProductVariantFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductEditFacade
	 */
	private $productEditFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductEditDataFactory
	 */
	private $productEditDataFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Image\ImageFacade
	 */
	private $imageFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductVariantService
	 */
	private $productVariantService;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler
	 */
	private $productPriceRecalculationScheduler;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler
	 */
	private $productAvailabilityRecalculationScheduler;

	public function __construct(
		EntityManager $em,
		ProductEditFacade $productEditFacade,
		ProductEditDataFactory $productEditDataFactory,
		ImageFacade $imageFacade,
		ProductVariantService $productVariantService,
		ProductPriceRecalculationScheduler $productPriceRecalculationScheduler,
		ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler
	) {
		$this->em = $em;
		$this->productEditFacade = $productEditFacade;
		$this->productEditDataFactory = $productEditDataFactory;
		$this->imageFacade = $imageFacade;
		$this->productVariantService = $productVariantService;
		$this->productPriceRecalculationScheduler = $productPriceRecalculationScheduler;
		$this->productAvailabilityRecalculationScheduler = $productAvailabilityRecalculationScheduler;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $mainProduct
	 * @param \SS6\ShopBundle\Model\Product\Product[] $variants
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function createVariant(Product $mainProduct, array $variants) {
		$this->productVariantService->checkProductIsNotMainVariant($mainProduct);

		try {
			$variants[] = $mainProduct;
			$mainProductEditData = $this->productEditDataFactory->createFromProduct($mainProduct);
			$newMainVariant = $this->productEditFacade->create($mainProductEditData);
			$newMainVariant->setVariants($variants);
			$this->imageFacade->copyImages($mainProduct, $newMainVariant);

			$this->em->flush();
		} catch (\Exception $exception) {
			$this->productAvailabilityRecalculationScheduler->cleanImmediatelyRecalculationSchedule();
			$this->productPriceRecalculationScheduler->cleanImmediatelyRecalculationSchedule();
			throw $exception;
		}

		return $newMainVariant;
	}

}
