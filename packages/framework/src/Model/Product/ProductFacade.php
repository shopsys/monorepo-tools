<?php

namespace Shopsys\FrameworkBundle\Model\Product;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Image\ImageFacade;
use Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupRepository;
use Shopsys\FrameworkBundle\Model\Product\Accessory\ProductAccessoryFactoryInterface;
use Shopsys\FrameworkBundle\Model\Product\Accessory\ProductAccessoryRepository;
use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade;
use Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterRepository;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValueFactoryInterface;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPriceFacade;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculation;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductSellingPrice;

class ProductFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityFacade
     */
    protected $productVisibilityFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterRepository
     */
    protected $parameterRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageFacade
     */
    protected $imageFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler
     */
    protected $productPriceRecalculationScheduler;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupRepository
     */
    protected $pricingGroupRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPriceFacade
     */
    protected $productManualInputPriceFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler
     */
    protected $productAvailabilityRecalculationScheduler;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    protected $friendlyUrlFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductHiddenRecalculator
     */
    protected $productHiddenRecalculator;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductSellingDeniedRecalculator
     */
    protected $productSellingDeniedRecalculator;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Accessory\ProductAccessoryRepository
     */
    protected $productAccessoryRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade
     */
    protected $availabilityFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade
     */
    protected $pluginCrudExtensionFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductFactoryInterface
     */
    protected $productFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Accessory\ProductAccessoryFactoryInterface
     */
    protected $productAccessoryFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface
     */
    protected $productCategoryDomainFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValueFactoryInterface
     */
    protected $productParameterValueFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityFactoryInterface
     */
    protected $productVisibilityFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculation
     */
    private $productPriceCalculation;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductRepository $productRepository
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityFacade $productVisibilityFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterRepository $parameterRepository
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Component\Image\ImageFacade $imageFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler $productPriceRecalculationScheduler
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupRepository $pricingGroupRepository
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPriceFacade $productManualInputPriceFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler
     * @param \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade $friendlyUrlFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductHiddenRecalculator $productHiddenRecalculator
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductSellingDeniedRecalculator $productSellingDeniedRecalculator
     * @param \Shopsys\FrameworkBundle\Model\Product\Accessory\ProductAccessoryRepository $productAccessoryRepository
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade $availabilityFacade
     * @param \Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade $pluginCrudExtensionFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductFactoryInterface $productFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\Accessory\ProductAccessoryFactoryInterface $productAccessoryFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface $productCategoryDomainFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValueFactoryInterface $productParameterValueFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityFactoryInterface $productVisibilityFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculation $productPriceCalculation
     */
    public function __construct(
        EntityManagerInterface $em,
        ProductRepository $productRepository,
        ProductVisibilityFacade $productVisibilityFacade,
        ParameterRepository $parameterRepository,
        Domain $domain,
        ImageFacade $imageFacade,
        ProductPriceRecalculationScheduler $productPriceRecalculationScheduler,
        PricingGroupRepository $pricingGroupRepository,
        ProductManualInputPriceFacade $productManualInputPriceFacade,
        ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler,
        FriendlyUrlFacade $friendlyUrlFacade,
        ProductHiddenRecalculator $productHiddenRecalculator,
        ProductSellingDeniedRecalculator $productSellingDeniedRecalculator,
        ProductAccessoryRepository $productAccessoryRepository,
        AvailabilityFacade $availabilityFacade,
        PluginCrudExtensionFacade $pluginCrudExtensionFacade,
        ProductFactoryInterface $productFactory,
        ProductAccessoryFactoryInterface $productAccessoryFactory,
        ProductCategoryDomainFactoryInterface $productCategoryDomainFactory,
        ProductParameterValueFactoryInterface $productParameterValueFactory,
        ProductVisibilityFactoryInterface $productVisibilityFactory,
        ProductPriceCalculation $productPriceCalculation
    ) {
        $this->em = $em;
        $this->productRepository = $productRepository;
        $this->productVisibilityFacade = $productVisibilityFacade;
        $this->parameterRepository = $parameterRepository;
        $this->domain = $domain;
        $this->imageFacade = $imageFacade;
        $this->productPriceRecalculationScheduler = $productPriceRecalculationScheduler;
        $this->pricingGroupRepository = $pricingGroupRepository;
        $this->productManualInputPriceFacade = $productManualInputPriceFacade;
        $this->productAvailabilityRecalculationScheduler = $productAvailabilityRecalculationScheduler;
        $this->friendlyUrlFacade = $friendlyUrlFacade;
        $this->productHiddenRecalculator = $productHiddenRecalculator;
        $this->productSellingDeniedRecalculator = $productSellingDeniedRecalculator;
        $this->productAccessoryRepository = $productAccessoryRepository;
        $this->availabilityFacade = $availabilityFacade;
        $this->pluginCrudExtensionFacade = $pluginCrudExtensionFacade;
        $this->productFactory = $productFactory;
        $this->productAccessoryFactory = $productAccessoryFactory;
        $this->productCategoryDomainFactory = $productCategoryDomainFactory;
        $this->productParameterValueFactory = $productParameterValueFactory;
        $this->productVisibilityFactory = $productVisibilityFactory;
        $this->productPriceCalculation = $productPriceCalculation;
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
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function create(ProductData $productData)
    {
        $product = $this->productFactory->create($productData);

        $this->em->persist($product);
        $this->em->flush($product);
        $this->setAdditionalDataAfterCreate($product, $productData);

        $this->pluginCrudExtensionFacade->saveAllData('product', $product->getId(), $productData->pluginData);

        return $product;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     */
    public function setAdditionalDataAfterCreate(Product $product, ProductData $productData)
    {
        // Persist of ProductCategoryDomain requires known primary key of Product
        // @see https://github.com/doctrine/doctrine2/issues/4869
        $product->setCategories($this->productCategoryDomainFactory, $productData->categoriesByDomainId);
        $this->em->flush($product);

        $this->saveParameters($product, $productData->parameters);
        $this->createProductVisibilities($product);
        $this->refreshProductManualInputPrices($product, $productData->manualInputPricesByPricingGroupId);
        $this->refreshProductAccessories($product, $productData->accessories);
        $this->productHiddenRecalculator->calculateHiddenForProduct($product);
        $this->productSellingDeniedRecalculator->calculateSellingDeniedForProduct($product);

        $this->imageFacade->uploadImages($product, $productData->images->uploadedFiles, null);
        $this->friendlyUrlFacade->createFriendlyUrls('front_product_detail', $product->getId(), $product->getNames());

        $this->productAvailabilityRecalculationScheduler->scheduleProductForImmediateRecalculation($product);
        $this->productVisibilityFacade->refreshProductsVisibilityForMarkedDelayed();
        $this->productPriceRecalculationScheduler->scheduleProductForImmediateRecalculation($product);
    }

    /**
     * @param int $productId
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function edit($productId, ProductData $productData)
    {
        $product = $this->productRepository->getById($productId);

        $product->edit($this->productCategoryDomainFactory, $productData, $this->productPriceRecalculationScheduler);

        $this->saveParameters($product, $productData->parameters);
        if (!$product->isMainVariant()) {
            $this->refreshProductManualInputPrices($product, $productData->manualInputPricesByPricingGroupId);
        } else {
            $product->refreshVariants($productData->variants);
        }
        $this->refreshProductAccessories($product, $productData->accessories);
        $this->em->flush();
        $this->productHiddenRecalculator->calculateHiddenForProduct($product);
        $this->productSellingDeniedRecalculator->calculateSellingDeniedForProduct($product);
        $this->imageFacade->saveImageOrdering($productData->images->orderedImages);
        $this->imageFacade->uploadImages($product, $productData->images->uploadedFiles, null);
        $this->imageFacade->deleteImages($product, $productData->images->imagesToDelete);
        $this->friendlyUrlFacade->saveUrlListFormData('front_product_detail', $product->getId(), $productData->urls);
        $this->friendlyUrlFacade->createFriendlyUrls('front_product_detail', $product->getId(), $product->getNames());

        $this->pluginCrudExtensionFacade->saveAllData('product', $product->getId(), $productData->pluginData);

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
        $productDeleteResult = $product->getProductDeleteResult();
        $productsForRecalculations = $productDeleteResult->getProductsForRecalculations();
        foreach ($productsForRecalculations as $productForRecalculations) {
            $this->productPriceRecalculationScheduler->scheduleProductForImmediateRecalculation($productForRecalculations);
            $productForRecalculations->markForVisibilityRecalculation();
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
    protected function saveParameters(Product $product, array $productParameterValuesData)
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
            $productParameterValue = $this->productParameterValueFactory->create(
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

        if (count($toFlush) > 0) {
            $this->em->flush($toFlush);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductSellingPrice[]
     */
    public function getAllProductSellingPricesIndexedByDomainId(Product $product)
    {
        $productSellingPrices = [];

        foreach ($this->pricingGroupRepository->getAll() as $pricingGroup) {
            $productSellingPrices[$pricingGroup->getDomainId()][$pricingGroup->getId()] = new ProductSellingPrice(
                $pricingGroup,
                $this->productPriceCalculation->calculatePrice($product, $pricingGroup->getDomainId(), $pricingGroup)
            );
        }

        return $productSellingPrices;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param string[] $manualInputPrices
     */
    protected function refreshProductManualInputPrices(Product $product, array $manualInputPrices)
    {
        foreach ($this->pricingGroupRepository->getAll() as $pricingGroup) {
            $this->productManualInputPriceFacade->refresh($product, $pricingGroup, $manualInputPrices[$pricingGroup->getId()]);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     */
    protected function createProductVisibilities(Product $product)
    {
        $toFlush = [];
        foreach ($this->domain->getAll() as $domainConfig) {
            $domainId = $domainConfig->getId();
            foreach ($this->pricingGroupRepository->getPricingGroupsByDomainId($domainId) as $pricingGroup) {
                $productVisibility = $this->productVisibilityFactory->create($product, $pricingGroup, $domainId);
                $this->em->persist($productVisibility);
                $toFlush[] = $productVisibility;
            }
        }

        if (count($toFlush) > 0) {
            $this->em->flush($toFlush);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $accessories
     */
    protected function refreshProductAccessories(Product $product, array $accessories)
    {
        $oldProductAccessories = $this->productAccessoryRepository->getAllByProduct($product);
        foreach ($oldProductAccessories as $oldProductAccessory) {
            $this->em->remove($oldProductAccessory);
        }
        $this->em->flush($oldProductAccessories);

        $toFlush = [];
        foreach ($accessories as $position => $accessory) {
            $newProductAccessory = $this->productAccessoryFactory->create($product, $accessory, $position);
            $this->em->persist($newProductAccessory);
            $toFlush[] = $newProductAccessory;
        }

        if (count($toFlush) > 0) {
            $this->em->flush($toFlush);
        }
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
