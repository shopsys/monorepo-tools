<?php

namespace SS6\ShopBundle\Model\Product;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Image\ImageFacade;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductEditDataFactory;
use SS6\ShopBundle\Model\Product\ProductEditFacade;
use SS6\ShopBundle\Model\Product\ProductService;
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
	 * @var \SS6\ShopBundle\Component\Image\ImageFacade
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

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductService
	 */
	private $productService;

	public function __construct(
		EntityManager $em,
		ProductEditFacade $productEditFacade,
		ProductEditDataFactory $productEditDataFactory,
		ImageFacade $imageFacade,
		ProductVariantService $productVariantService,
		ProductPriceRecalculationScheduler $productPriceRecalculationScheduler,
		ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler,
		ProductService $productService
	) {
		$this->em = $em;
		$this->productEditFacade = $productEditFacade;
		$this->productEditDataFactory = $productEditDataFactory;
		$this->imageFacade = $imageFacade;
		$this->productVariantService = $productVariantService;
		$this->productPriceRecalculationScheduler = $productPriceRecalculationScheduler;
		$this->productAvailabilityRecalculationScheduler = $productAvailabilityRecalculationScheduler;
		$this->productService = $productService;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $mainProduct
	 * @param \SS6\ShopBundle\Model\Product\Product[] $variants
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function createVariant(Product $mainProduct, array $variants) {
		$this->productVariantService->checkProductIsNotMainVariant($mainProduct);

		$mainVariantEditData = $this->productEditDataFactory->createFromProduct($mainProduct);
		$mainVariant = $this->productVariantService->createMainVariant($mainVariantEditData, $mainProduct, $variants);
		$this->em->persist($mainVariant);

		try {
			$toFlush = $mainVariant->getVariants();
			$toFlush[] = $mainVariant;
			$this->em->flush($toFlush);
			$this->productEditFacade->setAdditionalDataAfterCreate($mainVariant, $mainVariantEditData);
			$this->imageFacade->copyImages($mainProduct, $mainVariant);
		} catch (\Exception $exception) {
			$this->productAvailabilityRecalculationScheduler->cleanScheduleForImmediateRecalculation();
			$this->productPriceRecalculationScheduler->cleanScheduleForImmediateRecalculation();

			throw $exception;
		}

		return $mainVariant;
	}

}
