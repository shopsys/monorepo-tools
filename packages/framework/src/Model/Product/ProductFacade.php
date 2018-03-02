<?php

namespace Shopsys\FrameworkBundle\Model\Product;

use Doctrine\ORM\EntityManager;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Image\ImageFacade;
use Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupRepository;
use Shopsys\FrameworkBundle\Model\Product\Accessory\ProductAccessory;
use Shopsys\FrameworkBundle\Model\Product\Accessory\ProductAccessoryRepository;
use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade;
use Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterRepository;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValue;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPriceFacade;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;

class ProductFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityFacade
     */
    private $productVisibilityFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterRepository
     */
    private $parameterRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductService
     */
    private $productService;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageFacade
     */
    private $imageFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler
     */
    private $productPriceRecalculationScheduler;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupRepository
     */
    private $pricingGroupRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPriceFacade
     */
    private $productManualInputPriceFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler
     */
    private $productAvailabilityRecalculationScheduler;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    private $friendlyUrlFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductHiddenRecalculator
     */
    private $productHiddenRecalculator;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductSellingDeniedRecalculator
     */
    private $productSellingDeniedRecalculator;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Accessory\ProductAccessoryRepository
     */
    private $productAccessoryRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductVariantService
     */
    private $productVariantService;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade
     */
    private $availabilityFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade
     */
    private $pluginCrudExtensionFacade;

    public function __construct(
        EntityManager $em,
        ProductRepository $productRepository,
        ProductVisibilityFacade $productVisibilityFacade,
        ParameterRepository $parameterRepository,
        Domain $domain,
        ProductService $productService,
        ImageFacade $imageFacade,
        ProductPriceRecalculationScheduler $productPriceRecalculationScheduler,
        PricingGroupRepository $pricingGroupRepository,
        ProductManualInputPriceFacade $productManualInputPriceFacade,
        ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler,
        FriendlyUrlFacade $friendlyUrlFacade,
        ProductHiddenRecalculator $productHiddenRecalculator,
        ProductSellingDeniedRecalculator $productSellingDeniedRecalculator,
        ProductAccessoryRepository $productAccessoryRepository,
        ProductVariantService $productVariantService,
        AvailabilityFacade $availabilityFacade,
        PluginCrudExtensionFacade $pluginCrudExtensionFacade
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
        $this->pluginCrudExtensionFacade = $pluginCrudExtensionFacade;
    }

    /**
     * @param int $productId
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function getById($productId)
    {
        return $this->productRepository->getById($productId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductEditData $productEditData
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function create(ProductEditData $productEditData)
    {
        $product = Product::create($productEditData->productData);

        if ($product->isUsingStock()) {
            $defaultInStockAvailability = $this->availabilityFacade->getDefaultInStockAvailability();
            $product->setCalculatedAvailability($defaultInStockAvailability);
            $product->markForAvailabilityRecalculation();
        }

        $this->em->persist($product);
        $this->em->flush($product);
        $this->setAdditionalDataAfterCreate($product, $productEditData);

        $this->pluginCrudExtensionFacade->saveAllData('product', $product->getId(), $productEditData->pluginData);

        return $product;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductEditData $productEditData
     */
    public function setAdditionalDataAfterCreate(Product $product, ProductEditData $productEditData)
    {
        // Persist of ProductCategoryDomain requires known primary key of Product
        // @see https://github.com/doctrine/doctrine2/issues/4869
        $product->setCategories($productEditData->productData->categoriesByDomainId);
        $this->em->flush($product);

        $this->saveParameters($product, $productEditData->parameters);
        $this->createProductDomains($product, $this->domain->getAll());
        $this->createProductVisibilities($product);
        $this->refreshProductDomains($product, $productEditData);
        $this->refreshProductManualInputPrices($product, $productEditData->manualInputPricesByPricingGroupId);
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
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductEditData $productEditData
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function edit($productId, ProductEditData $productEditData)
    {
        $product = $this->productRepository->getById($productId);

        $this->productService->edit($product, $productEditData->productData);

        $this->saveParameters($product, $productEditData->parameters);
        $this->refreshProductDomains($product, $productEditData);
        if (!$product->isMainVariant()) {
            $this->refreshProductManualInputPrices($product, $productEditData->manualInputPricesByPricingGroupId);
        } else {
            $this->productVariantService->refreshProductVariants($product, $productEditData->variants);
        }
        $this->refreshProductAccessories($product, $productEditData->accessories);
        $this->em->flush();
        $this->productHiddenRecalculator->calculateHiddenForProduct($product);
        $this->productSellingDeniedRecalculator->calculateSellingDeniedForProduct($product);
        $this->imageFacade->saveImageOrdering($productEditData->orderedImagesById);
        $this->imageFacade->uploadImages($product, $productEditData->imagesToUpload, null);
        $this->imageFacade->deleteImages($product, $productEditData->imagesToDelete);
        $this->friendlyUrlFacade->saveUrlListFormData('front_product_detail', $product->getId(), $productEditData->urls);
        $this->friendlyUrlFacade->createFriendlyUrls('front_product_detail', $product->getId(), $product->getNames());

        $this->pluginCrudExtensionFacade->saveAllData('product', $product->getId(), $productEditData->pluginData);

        $this->productAvailabilityRecalculationScheduler->scheduleProductForImmediateRecalculation($product);
        $this->productVisibilityFacade->refreshProductsVisibilityForMarkedDelayed();
        $this->productPriceRecalculationScheduler->scheduleProductForImmediateRecalculation($product);

        return $product;
    }

    /**
     * @param int $productId
     */
    public function delete($productId)
    {
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

        $this->pluginCrudExtensionFacade->removeAllData('product', $product->getId());
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValueData[] $productParameterValuesData
     */
    private function saveParameters(Product $product, array $productParameterValuesData)
    {
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
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig[] $domains
     */
    private function createProductDomains(Product $product, array $domains)
    {
        $toFlush = [];
        foreach ($domains as $domain) {
            $productDomain = new ProductDomain($product, $domain->getId());
            $this->em->persist($productDomain);
            $toFlush[] = $productDomain;
        }
        $this->em->flush($toFlush);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductEditData $productEditData
     */
    private function refreshProductDomains(Product $product, ProductEditData $productEditData)
    {
        $productDomains = $this->productRepository->getProductDomainsByProductIndexedByDomainId($product);
        $seoTitles = $productEditData->seoTitles;
        $seoMetaDescriptions = $productEditData->seoMetaDescriptions;
        $seoH1s = $productEditData->seoH1s;
        if (!$product->isVariant()) {
            $descriptions = $productEditData->descriptions;
            $shortDescriptions = $productEditData->shortDescriptions;
        }

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
            if (!empty($seoH1s)) {
                $productDomain->setSeoH1($seoH1s[$domainId]);
            }
        }

        $this->em->flush($productDomains);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductSellingPrice[]
     */
    public function getAllProductSellingPricesIndexedByDomainId(Product $product)
    {
        return $this->productService->getProductSellingPricesIndexedByDomainIdAndPricingGroupId(
            $product,
            $this->pricingGroupRepository->getAll()
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param string[] $manualInputPrices
     */
    private function refreshProductManualInputPrices(Product $product, array $manualInputPrices)
    {
        if ($product->getPriceCalculationType() === Product::PRICE_CALCULATION_TYPE_MANUAL) {
            foreach ($this->pricingGroupRepository->getAll() as $pricingGroup) {
                $this->productManualInputPriceFacade->refresh($product, $pricingGroup, $manualInputPrices[$pricingGroup->getId()]);
            }
        } else {
            $this->productManualInputPriceFacade->deleteByProduct($product);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     */
    private function createProductVisibilities(Product $product)
    {
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
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $accessories
     */
    private function refreshProductAccessories(Product $product, array $accessories)
    {
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
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function getOneByCatnumExcludeMainVariants($productCatnum)
    {
        return $this->productRepository->getOneByCatnumExcludeMainVariants($productCatnum);
    }
}
