<?php

namespace Shopsys\FrameworkBundle\Model\Product;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Image\ImageFacade;
use Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use Shopsys\FrameworkBundle\Model\Product\Accessory\ProductAccessoryRepository;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterRepository;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValueData;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductInputPriceFacade;

class ProductEditDataFactory
{
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
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductDataFactory
     */
    protected $productDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductInputPriceFacade
     */
    protected $productInputPriceFacade;

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
        Domain $domain,
        ProductRepository $productRepository,
        ParameterRepository $parameterRepository,
        ProductDataFactory $productDataFactory,
        ProductInputPriceFacade $productInputPriceFacade,
        FriendlyUrlFacade $friendlyUrlFacade,
        ProductAccessoryRepository $productAccessoryRepository,
        ImageFacade $imageFacade,
        PluginCrudExtensionFacade $pluginDataFormExtensionFacade
    ) {
        $this->domain = $domain;
        $this->productRepository = $productRepository;
        $this->parameterRepository = $parameterRepository;
        $this->productDataFactory = $productDataFactory;
        $this->productInputPriceFacade = $productInputPriceFacade;
        $this->friendlyUrlFacade = $friendlyUrlFacade;
        $this->productAccessoryRepository = $productAccessoryRepository;
        $this->imageFacade = $imageFacade;
        $this->pluginDataFormExtensionFacade = $pluginDataFormExtensionFacade;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductEditData
     */
    public function createDefault()
    {
        $productEditData = new ProductEditData();
        $productEditData->productData = $this->productDataFactory->createDefault();

        $productParameterValuesData = [];
        $productEditData->parameters = $productParameterValuesData;

        $nullForAllDomains = $this->getNullForAllDomains();

        $productEditData->manualInputPricesByPricingGroupId = [];
        $productEditData->seoTitles = $nullForAllDomains;
        $productEditData->seoH1s = $nullForAllDomains;
        $productEditData->seoMetaDescriptions = $nullForAllDomains;
        $productEditData->descriptions = $nullForAllDomains;
        $productEditData->shortDescriptions = $nullForAllDomains;
        $productEditData->accessories = [];

        return $productEditData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductEditData
     */
    public function createFromProduct(Product $product)
    {
        $productEditData = $this->createDefault();

        $productEditData->productData = $this->productDataFactory->createFromProduct($product);
        $productEditData->parameters = $this->getParametersData($product);
        try {
            $productEditData->manualInputPricesByPricingGroupId = $this->productInputPriceFacade->getManualInputPricesDataIndexedByPricingGroupId($product);
        } catch (\Shopsys\FrameworkBundle\Model\Product\Pricing\Exception\MainVariantPriceCalculationException $ex) {
            $productEditData->manualInputPricesByPricingGroupId = null;
        }
        $productEditData->accessories = $this->getAccessoriesData($product);
        $productEditData->images->orderedImages = $this->imageFacade->getImagesByEntityIndexedById($product, null);
        $productEditData->variants = $product->getVariants();

        $this->setMultidomainData($product, $productEditData);

        $productEditData->pluginData = $this->pluginDataFormExtensionFacade->getAllData('product', $product->getId());

        return $productEditData;
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
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductEditData $productEditData
     */
    protected function setMultidomainData(Product $product, ProductEditData $productEditData)
    {
        $productDomains = $this->productRepository->getProductDomainsByProductIndexedByDomainId($product);
        foreach ($productDomains as $domainId => $productDomain) {
            $productEditData->seoTitles[$domainId] = $productDomain->getSeoTitle();
            $productEditData->seoMetaDescriptions[$domainId] = $productDomain->getSeoMetaDescription();
            $productEditData->seoH1s[$domainId] = $productDomain->getSeoH1();
            $productEditData->descriptions[$domainId] = $productDomain->getDescription();
            $productEditData->shortDescriptions[$domainId] = $productDomain->getShortDescription();

            $productEditData->urls->mainFriendlyUrlsByDomainId[$domainId] =
                $this->friendlyUrlFacade->findMainFriendlyUrl($domainId, 'front_product_detail', $product->getId());
        }
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
