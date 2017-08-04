<?php

namespace Shopsys\ShopBundle\Model\Product;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Image\ImageFacade;
use Shopsys\ShopBundle\Component\Plugin\PluginCrudExtensionFacade;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use Shopsys\ShopBundle\Model\Product\Accessory\ProductAccessoryRepository;
use Shopsys\ShopBundle\Model\Product\Parameter\ParameterRepository;
use Shopsys\ShopBundle\Model\Product\Parameter\ProductParameterValueData;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductInputPriceFacade;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductDataFactory;

class ProductEditDataFactory
{
    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Parameter\ParameterRepository
     */
    private $parameterRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductDataFactory
     */
    private $productDataFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductInputPriceFacade
     */
    private $productInputPriceFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    private $friendlyUrlFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Accessory\ProductAccessoryRepository
     */
    private $productAccessoryRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\ImageFacade
     */
    private $imageFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Plugin\PluginCrudExtensionFacade
     */
    private $pluginDataFormExtensionFacade;

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
     * @return \Shopsys\ShopBundle\Model\Product\ProductEditData
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
        $productEditData->heurekaCpcValues = $nullForAllDomains;
        foreach ($this->domain->getAllIds() as $domainId) {
            $productEditData->showInZboziFeedIndexedByDomainId[$domainId] = true;
        }
        $productEditData->zboziCpcValues = $nullForAllDomains;
        $productEditData->zboziCpcSearchValues = $nullForAllDomains;

        return $productEditData;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return \Shopsys\ShopBundle\Model\Product\ProductEditData
     */
    public function createFromProduct(Product $product)
    {
        $productEditData = $this->createDefault();

        $productEditData->productData = $this->productDataFactory->createFromProduct($product);
        $productEditData->parameters = $this->getParametersData($product);
        try {
            $productEditData->manualInputPricesByPricingGroupId = $this->productInputPriceFacade->getManualInputPricesDataIndexedByPricingGroupId($product);
        } catch (\Shopsys\ShopBundle\Model\Product\Pricing\Exception\MainVariantPriceCalculationException $ex) {
            $productEditData->manualInputPricesByPricingGroupId = null;
        }
        $productEditData->accessories = $this->getAccessoriesData($product);
        $productEditData->orderedImagesById = $this->imageFacade->getImagesByEntityIndexedById($product, null);
        $productEditData->variants = $product->getVariants();

        $this->setMultidomainData($product, $productEditData);

        $productEditData->pluginData = $this->pluginDataFormExtensionFacade->getAllData('product', $product->getId());

        return $productEditData;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return \Shopsys\ShopBundle\Model\Product\Product[]
     */
    private function getAccessoriesData(Product $product)
    {
        $productAccessoriesByPosition = [];
        foreach ($this->productAccessoryRepository->getAllByProduct($product) as $productAccessory) {
            $productAccessoriesByPosition[$productAccessory->getPosition()] = $productAccessory->getAccessory();
        }

        return $productAccessoriesByPosition;
    }

    /**
     * @param Product $product
     * @return \Shopsys\ShopBundle\Model\Product\Parameter\ProductParameterValueData[]
     */
    private function getParametersData(Product $product)
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
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param \Shopsys\ShopBundle\Model\Product\ProductEditData $productEditData
     */
    private function setMultidomainData(Product $product, ProductEditData $productEditData)
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
            $productEditData->heurekaCpcValues[$domainId] = $productDomain->getHeurekaCpc();
            $productEditData->showInZboziFeedIndexedByDomainId[$domainId] = $productDomain->getShowInZboziFeed();
            $productEditData->zboziCpcValues[$domainId] = $productDomain->getZboziCpc();
            $productEditData->zboziCpcSearchValues[$domainId] = $productDomain->getZboziCpcSearch();
        }
    }

    /**
     * @return array
     */
    private function getNullForAllDomains()
    {
        $nullForAllDomains = [];
        foreach ($this->domain->getAll() as $domainConfig) {
            $nullForAllDomains[$domainConfig->getId()] = null;
        }

        return $nullForAllDomains;
    }
}
