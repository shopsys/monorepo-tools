<?php

namespace Shopsys\ShopBundle\Model\Product;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Image\ImageFacade;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupRepository;
use Shopsys\ShopBundle\Model\Product\Accessory\ProductAccessory;
use Shopsys\ShopBundle\Model\Product\Accessory\ProductAccessoryRepository;
use Shopsys\ShopBundle\Model\Product\Availability\AvailabilityFacade;
use Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use Shopsys\ShopBundle\Model\Product\Parameter\ParameterRepository;
use Shopsys\ShopBundle\Model\Product\Parameter\ProductParameterValue;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductManualInputPriceFacade;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductDomain;
use Shopsys\ShopBundle\Model\Product\ProductEditData;
use Shopsys\ShopBundle\Model\Product\ProductHiddenRecalculator;
use Shopsys\ShopBundle\Model\Product\ProductRepository;
use Shopsys\ShopBundle\Model\Product\ProductSellingDeniedRecalculator;
use Shopsys\ShopBundle\Model\Product\ProductService;
use Shopsys\ShopBundle\Model\Product\ProductVisibility;
use Shopsys\ShopBundle\Model\Product\ProductVisibilityFacade;

class ProductEditFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\ProductVisibilityFacade
	 */
	private $productVisibilityFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\Parameter\ParameterRepository
	 */
	private $parameterRepository;

	/**
	 * @var \Shopsys\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\ProductService
	 */
	private $productService;

	/**
	 * @var \Shopsys\ShopBundle\Component\Image\ImageFacade
	 */
	private $imageFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler
	 */
	private $productPriceRecalculationScheduler;

	/**
	 * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupRepository
	 */
	private $pricingGroupRepository;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductManualInputPriceFacade
	 */
	private $productManualInputPriceFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler
	 */
	private $productAvailabilityRecalculationScheduler;

	/**
	 * @var \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
	 */
	private $friendlyUrlFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\ProductHiddenRecalculator
	 */
	private $productHiddenRecalculator;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\ProductSellingDeniedRecalculator
	 */
	private $productSellingDeniedRecalculator;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\Accessory\ProductAccessoryRepository
	 */
	private $productAccessoryRepository;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\ProductVariantService
	 */
	private $productVariantService;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\Availability\AvailabilityFacade
	 */
	private $availabilityFacade;

	public function __construct(
		EntityManager $em,
		ProductRepository $productRepository,
		ProductVisibilityFacade $productVisibilityFacade,
		ParameterRepository $parameterRepository,
		Domain $domain,
		ProductService $productService,
		ImageFacade	$imageFacade,
		ProductPriceRecalculationScheduler $productPriceRecalculationScheduler,
		PricingGroupRepository $pricingGroupRepository,
		ProductManualInputPriceFacade $productManualInputPriceFacade,
		ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler,
		FriendlyUrlFacade $friendlyUrlFacade,
		ProductHiddenRecalculator $productHiddenRecalculator,
		ProductSellingDeniedRecalculator $productSellingDeniedRecalculator,
		ProductAccessoryRepository $productAccessoryRepository,
		ProductVariantService $productVariantService,
		AvailabilityFacade $availabilityFacade
	) {
		$this->em = $em;
		$this->productRepository = $productRepository;
		$this->productVisibilityFacade = $productVisibilityFacade;
		$this->parameterRepository = $parameterRepository;
		$this->domain = $domain;
		$this->productService = $productService;
		$this->imageFacade = $imageFacade;
		$this->productPriceRecalculationScheduler = $productPriceRecalculationScheduler;
		$this->pricingGroupRepository = $pricingGroupRepository;
		$this->productManualInputPriceFacade = $productManualInputPriceFacade;
		$this->productAvailabilityRecalculationScheduler = $productAvailabilityRecalculationScheduler;
		$this->friendlyUrlFacade = $friendlyUrlFacade;
		$this->productHiddenRecalculator = $productHiddenRecalculator;
		$this->productSellingDeniedRecalculator = $productSellingDeniedRecalculator;
		$this->productAccessoryRepository = $productAccessoryRepository;
		$this->productVariantService = $productVariantService;
		$this->availabilityFacade = $availabilityFacade;
	}

	/**
	 * @param int $productId
	 * @return \Shopsys\ShopBundle\Model\Product\Product
	 */
	public function getById($productId) {
		return $this->productRepository->getById($productId);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\ProductEditData $productEditData
	 * @return \Shopsys\ShopBundle\Model\Product\Product
	 */
	public function create(ProductEditData $productEditData) {
		$product = Product::create($productEditData->productData);

		if ($product->isUsingStock()) {
			$defaultInStockAvailability = $this->availabilityFacade->getDefaultInStockAvailability();
			$product->setCalculatedAvailability($defaultInStockAvailability);
			$product->markForAvailabilityRecalculation();
		}

		$this->em->persist($product);
		$this->em->flush($product);
		$this->setAdditionalDataAfterCreate($product, $productEditData);

		return $product;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 * @param \Shopsys\ShopBundle\Model\Product\ProductEditData $productEditData
	 */
	public function setAdditionalDataAfterCreate(Product $product, ProductEditData $productEditData) {
		// Persist of ProductCategoryDomain requires known primary key of Product
		// @see https://github.com/doctrine/doctrine2/issues/4869
		$product->setCategories($productEditData->productData->categoriesByDomainId);
		$this->em->flush($product);

		$this->saveParameters($product, $productEditData->parameters);
		$this->createProductDomains($product, $this->domain->getAll());
		$this->createProductVisibilities($product);
		$this->refreshProductDomains($product, $productEditData);
		$this->refreshProductManualInputPrices($product, $productEditData->manualInputPrices);
		$this->refreshProductAccessories($product, $productEditData->accessories);
		$this->productHiddenRecalculator->calculateHiddenForProduct($product);
		$this->productSellingDeniedRecalculator->calculateSellingDeniedForProduct($product);

		$this->imageFacade->uploadImages($product, $productEditData->imagesToUpload, null);
		$this->friendlyUrlFacade->createFriendlyUrls('front_product_detail', $product->getId(), $product->getNames());

		$this->productAvailabilityRecalculationScheduler->scheduleProductForImmediateRecalculation($product);
		$this->productVisibilityFacade->refreshProductsVisibilityForMarkedDelayed();
		$this->productPriceRecalculationScheduler->scheduleProductForImmediateRecalculation($product);
	}

	/**
	 * @param int $productId
	 * @param \Shopsys\ShopBundle\Model\Product\ProductEditData $productEditData
	 * @return \Shopsys\ShopBundle\Model\Product\Product
	 */
	public function edit($productId, ProductEditData $productEditData) {
		$product = $this->productRepository->getById($productId);

		$this->productService->edit($product, $productEditData->productData);

		$this->saveParameters($product, $productEditData->parameters);
		$this->refreshProductDomains($product, $productEditData);
		if (!$product->isMainVariant()) {
			$this->refreshProductManualInputPrices($product, $productEditData->manualInputPrices);
		} else {
			$this->productVariantService->refreshProductVariants($product, $productEditData->variants);
		}
		$this->refreshProductAccessories($product, $productEditData->accessories);
		$this->em->flush();
		$this->productHiddenRecalculator->calculateHiddenForProduct($product);
		$this->productSellingDeniedRecalculator->calculateSellingDeniedForProduct($product);
		$this->imageFacade->saveImagePositions($productEditData->imagePositions);
		$this->imageFacade->uploadImages($product, $productEditData->imagesToUpload, null);
		$this->imageFacade->deleteImages($product, $productEditData->imagesToDelete);
		$this->friendlyUrlFacade->saveUrlListFormData('front_product_detail', $product->getId(), $productEditData->urls);
		$this->friendlyUrlFacade->createFriendlyUrls('front_product_detail', $product->getId(), $product->getNames());

		$this->productAvailabilityRecalculationScheduler->scheduleProductForImmediateRecalculation($product);
		$this->productVisibilityFacade->refreshProductsVisibilityForMarkedDelayed();
		$this->productPriceRecalculationScheduler->scheduleProductForImmediateRecalculation($product);

		return $product;
	}

	/**
	 * @param int $productId
	 */
	public function delete($productId) {
		$product = $this->productRepository->getById($productId);
		$productDeleteResult = $this->productService->delete($product);
		$productsForRecalculations = $productDeleteResult->getProductsForRecalculations();
		foreach ($productsForRecalculations as $productForRecalculations) {
			$this->productPriceRecalculationScheduler->scheduleProductForImmediateRecalculation($productForRecalculations);
			$this->productService->markProductForVisibilityRecalculation($productForRecalculations);
			$this->productAvailabilityRecalculationScheduler->scheduleProductForImmediateRecalculation($productForRecalculations);
		}
		$this->em->remove($product);
		$this->em->flush();
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 * @param \Shopsys\ShopBundle\Model\Product\Parameter\ProductParameterValueData[] $productParameterValuesData
	 */
	private function saveParameters(Product $product, array $productParameterValuesData) {
		// Doctrine runs INSERTs before DELETEs in UnitOfWork. In case of UNIQUE constraint
		// in database, this leads in trying to insert duplicate entry.
		// That's why it's necessary to do remove and flush first.

		$oldProductParameterValues = $this->parameterRepository->getProductParameterValuesByProduct($product);
		foreach ($oldProductParameterValues as $oldProductParameterValue) {
			$this->em->remove($oldProductParameterValue);
		}
		$this->em->flush($oldProductParameterValues);

		$toFlush = [];
		foreach ($productParameterValuesData as $productParameterValueData) {
			$productParameterValue = new ProductParameterValue(
				$product,
				$productParameterValueData->parameter,
				$this->parameterRepository->findOrCreateParameterValueByValueTextAndLocale(
					$productParameterValueData->parameterValueData->text,
					$productParameterValueData->parameterValueData->locale
				)
			);
			$this->em->persist($productParameterValue);
			$toFlush[] = $productParameterValue;
		}
		$this->em->flush($toFlush);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig[] $domains
	 */
	private function createProductDomains(Product $product, array $domains) {
		$toFlush = [];
		foreach ($domains as $domain) {
			$productDomain = new ProductDomain($product, $domain->getId());
			$this->em->persist($productDomain);
			$toFlush[] = $productDomain;
		}
		$this->em->flush($toFlush);

	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 * @param \Shopsys\ShopBundle\Model\Product\ProductEditData $productEditData
	 */
	private function refreshProductDomains(Product $product, ProductEditData $productEditData) {
		$productDomains = $this->productRepository->getProductDomainsByProductIndexedByDomainId($product);
		$seoTitles = $productEditData->seoTitles;
		$seoMetaDescriptions = $productEditData->seoMetaDescriptions;
		if (!$product->isVariant()) {
			$descriptions = $productEditData->descriptions;
			$shortDescriptions = $productEditData->shortDescriptions;
		}
		$heurekaCpcValues = $productEditData->heurekaCpcValues;
		$showInZboziFeed = $productEditData->showInZboziFeed;
		$zboziCpcValues = $productEditData->zboziCpcValues;
		$zboziCpcSearchValues = $productEditData->zboziCpcSearchValues;

		foreach ($productDomains as $domainId => $productDomain) {
			if (!empty($seoTitles)) {
				$productDomain->setSeoTitle($seoTitles[$domainId]);
			}
			if (!empty($seoMetaDescriptions)) {
				$productDomain->setSeoMetaDescription($seoMetaDescriptions[$domainId]);
			}
			if (!empty($descriptions)) {
				$productDomain->setDescription($descriptions[$domainId]);
			}
			if (!empty($shortDescriptions)) {
				$productDomain->setShortDescription($shortDescriptions[$domainId]);
			}
			if (!empty($heurekaCpcValues)) {
				$productDomain->setHeurekaCpc($heurekaCpcValues[$domainId]);
			}
			if (!empty($showInZboziFeed)) {
				$productDomain->setShowInZboziFeed($showInZboziFeed[$domainId]);
			}
			if (!empty($zboziCpcValues)) {
				$productDomain->setZboziCpc($zboziCpcValues[$domainId]);
			}
			if (!empty($zboziCpcSearchValues)) {
				$productDomain->setZboziCpcSearch($zboziCpcSearchValues[$domainId]);
			}
		}

		$this->em->flush($productDomains);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 * @return \Shopsys\ShopBundle\Model\Product\Pricing\ProductSellingPrice[]
	 */
	public function getAllProductSellingPricesIndexedByDomainId(Product $product) {
		return $this->productService->getProductSellingPricesIndexedByDomainIdAndPricingGroupId(
			$product,
			$this->pricingGroupRepository->getAll()
		);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 * @param string[] $manualInputPrices
	 */
	private function refreshProductManualInputPrices(Product $product, array $manualInputPrices) {
		if ($product->getPriceCalculationType() === Product::PRICE_CALCULATION_TYPE_MANUAL) {
			foreach ($this->pricingGroupRepository->getAll() as $pricingGroup) {
				$this->productManualInputPriceFacade->refresh($product, $pricingGroup, $manualInputPrices[$pricingGroup->getId()]);
			}
		} else {
			$this->productManualInputPriceFacade->deleteByProduct($product);
		}
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 */
	private function createProductVisibilities(Product $product) {
		$toFlush = [];
		foreach ($this->domain->getAll() as $domainConfig) {
			$domainId = $domainConfig->getId();
			foreach ($this->pricingGroupRepository->getPricingGroupsByDomainId($domainId) as $pricingGroup) {
				$productVisibility = new ProductVisibility($product, $pricingGroup, $domainId);
				$this->em->persist($productVisibility);
				$toFlush[] = $productVisibility;
			}
		}
		$this->em->flush($toFlush);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product[] $accessories
	 */
	private function refreshProductAccessories(Product $product, array $accessories) {
		$oldProductAccessories = $this->productAccessoryRepository->getAllByProduct($product);
		foreach ($oldProductAccessories as $oldProductAccessory) {
			$this->em->remove($oldProductAccessory);
		}
		$this->em->flush($oldProductAccessories);

		$toFlush = [];
		foreach ($accessories as $position => $accessory) {
			$newProductAccessory = new ProductAccessory($product, $accessory, $position);
			$this->em->persist($newProductAccessory);
			$toFlush[] = $newProductAccessory;
		}
		$this->em->flush($toFlush);
	}

	/**
	 * @param string $productCatnum
	 * @return \Shopsys\ShopBundle\Model\Product\Product
	 */
	public function getOneByCatnumExcludeMainVariants($productCatnum) {
		return $this->productRepository->getOneByCatnumExcludeMainVariants($productCatnum);
	}

}
