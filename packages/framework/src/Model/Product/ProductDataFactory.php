<?php

namespace Shopsys\FrameworkBundle\Model\Product;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Image\ImageFacade;
use Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade;
use Shopsys\FrameworkBundle\Model\Product\Accessory\ProductAccessoryRepository;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterRepository;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValueData;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductInputPriceFacade;
use Shopsys\FrameworkBundle\Model\Product\Unit\UnitFacade;

class ProductDataFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade
     */
    protected $vatFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductInputPriceFacade
     */
    protected $productInputPriceFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Unit\UnitFacade
     */
    protected $unitFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterRepository
     */
    protected $parameterRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    protected $friendlyUrlFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Accessory\ProductAccessoryRepository
     */
    protected $productAccessoryRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageFacade
     */
    protected $imageFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade
     */
    protected $pluginDataFormExtensionFacade;

    public function __construct(
        VatFacade $vatFacade,
        ProductInputPriceFacade $productInputPriceFacade,
        UnitFacade $unitFacade,
        Domain $domain,
        ProductRepository $productRepository,
        ParameterRepository $parameterRepository,
        FriendlyUrlFacade $friendlyUrlFacade,
        ProductAccessoryRepository $productAccessoryRepository,
        ImageFacade $imageFacade,
        PluginCrudExtensionFacade $pluginDataFormExtensionFacade
    ) {
        $this->vatFacade = $vatFacade;
        $this->productInputPriceFacade = $productInputPriceFacade;
        $this->unitFacade = $unitFacade;
        $this->domain = $domain;
        $this->productRepository = $productRepository;
        $this->parameterRepository = $parameterRepository;
        $this->friendlyUrlFacade = $friendlyUrlFacade;
        $this->productAccessoryRepository = $productAccessoryRepository;
        $this->imageFacade = $imageFacade;
        $this->pluginDataFormExtensionFacade = $pluginDataFormExtensionFacade;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductData
     */
    public function createDefault()
    {
        $productData = new ProductData();

        $productData->vat = $this->vatFacade->getDefaultVat();
        $productData->unit = $this->unitFacade->getDefaultUnit();

        $productParameterValuesData = [];
        $productData->parameters = $productParameterValuesData;

        $nullForAllDomains = $this->getNullForAllDomains();

        $productData->manualInputPricesByPricingGroupId = [];
        $productData->seoTitles = $nullForAllDomains;
        $productData->seoH1s = $nullForAllDomains;
        $productData->seoMetaDescriptions = $nullForAllDomains;
        $productData->descriptions = $nullForAllDomains;
        $productData->shortDescriptions = $nullForAllDomains;
        $productData->accessories = [];

        return $productData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductData
     */
    public function createFromProduct(Product $product)
    {
        $productData = $this->createDefault();

        $translations = $product->getTranslations();
        $names = [];
        $variantAliases = [];
        foreach ($translations as $translation) {
            $names[$translation->getLocale()] = $translation->getName();
            $variantAliases[$translation->getLocale()] = $translation->getVariantAlias();
        }

        foreach ($this->domain->getAllIds() as $domainId) {
            $productData->shortDescriptions[$domainId] = $product->getShortDescription($domainId);
            $productData->descriptions[$domainId] = $product->getDescription($domainId);
            $productData->seoH1s[$domainId] = $product->getSeoH1($domainId);
            $productData->seoTitles[$domainId] = $product->getSeoTitle($domainId);
            $productData->seoMetaDescriptions[$domainId] = $product->getSeoMetaDescription($domainId);
        }
        $productData->name = $names;
        $productData->variantAlias = $variantAliases;

        $productData->catnum = $product->getCatnum();
        $productData->partno = $product->getPartno();
        $productData->ean = $product->getEan();
        $productData->price = $this->productInputPriceFacade->getInputPrice($product);
        $productData->vat = $product->getVat();
        $productData->sellingFrom = $product->getSellingFrom();
        $productData->sellingTo = $product->getSellingTo();
        $productData->sellingDenied = $product->isSellingDenied();
        $productData->flags = $product->getFlags()->toArray();
        $productData->usingStock = $product->isUsingStock();
        $productData->stockQuantity = $product->getStockQuantity();
        $productData->unit = $product->getUnit();
        $productData->availability = $product->getAvailability();
        $productData->outOfStockAvailability = $product->getOutOfStockAvailability();
        $productData->outOfStockAction = $product->getOutOfStockAction();

        $productData->hidden = $product->isHidden();
        $productData->categoriesByDomainId = $product->getCategoriesIndexedByDomainId();
        $productData->priceCalculationType = $product->getPriceCalculationType();
        $productData->brand = $product->getBrand();
        $productData->orderingPriority = $product->getOrderingPriority();

        $productData->parameters = $this->getParametersData($product);
        try {
            $productData->manualInputPricesByPricingGroupId = $this->productInputPriceFacade->getManualInputPricesDataIndexedByPricingGroupId($product);
        } catch (\Shopsys\FrameworkBundle\Model\Product\Pricing\Exception\MainVariantPriceCalculationException $ex) {
            $productData->manualInputPricesByPricingGroupId = null;
        }
        $productData->accessories = $this->getAccessoriesData($product);
        $productData->images->orderedImages = $this->imageFacade->getImagesByEntityIndexedById($product, null);
        $productData->variants = $product->getVariants();
        $productData->pluginData = $this->pluginDataFormExtensionFacade->getAllData('product', $product->getId());

        return $productData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Model\Product\Product[]
     */
    protected function getAccessoriesData(Product $product)
    {
        $productAccessoriesByPosition = [];
        foreach ($this->productAccessoryRepository->getAllByProduct($product) as $productAccessory) {
            $productAccessoriesByPosition[$productAccessory->getPosition()] = $productAccessory->getAccessory();
        }

        return $productAccessoriesByPosition;
    }

    /**
     * @param Product $product
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValueData[]
     */
    protected function getParametersData(Product $product)
    {
        $productParameterValuesData = [];
        $productParameterValues = $this->parameterRepository->getProductParameterValuesByProduct($product);
        foreach ($productParameterValues as $productParameterValue) {
            $productParameterValueData = new ProductParameterValueData();
            $productParameterValueData->setFromEntity($productParameterValue);
            $productParameterValuesData[] = $productParameterValueData;
        }

        return $productParameterValuesData;
    }

    /**
     * @return array
     */
    protected function getNullForAllDomains()
    {
        $nullForAllDomains = [];
        foreach ($this->domain->getAll() as $domainConfig) {
            $nullForAllDomains[$domainConfig->getId()] = null;
        }

        return $nullForAllDomains;
    }
}
