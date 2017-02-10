<?php

namespace Shopsys\ShopBundle\Model\Product;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Image\ImageFacade;
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

    public function __construct(
        Domain $domain,
        ProductRepository $productRepository,
        ParameterRepository $parameterRepository,
        ProductDataFactory $productDataFactory,
        ProductInputPriceFacade $productInputPriceFacade,
        FriendlyUrlFacade $friendlyUrlFacade,
        ProductAccessoryRepository $productAccessoryRepository,
        ImageFacade $imageFacade
    ) {
        $this->domain = $domain;
        $this->productRepository = $productRepository;
        $this->parameterRepository = $parameterRepository;
        $this->productDataFactory = $productDataFactory;
        $this->productInputPriceFacade = $productInputPriceFacade;
        $this->friendlyUrlFacade = $friendlyUrlFacade;
        $this->productAccessoryRepository = $productAccessoryRepository;
        $this->imageFacade = $imageFacade;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\ProductEditData
     */
    public function createDefault() {
        $productEditData = new ProductEditData();
        $productEditData->productData = $this->productDataFactory->createDefault();

        $productParameterValuesData = [];
        $productEditData->parameters = $productParameterValuesData;

        $nullForAllDomains = $this->getNullForAllDomains();

        $productEditData->manualInputPrices = [];
        $productEditData->seoTitles = $nullForAllDomains;
        $productEditData->seoMetaDescriptions = $nullForAllDomains;
        $productEditData->descriptions = $nullForAllDomains;
        $productEditData->shortDescriptions = $nullForAllDomains;
        $productEditData->accessories = [];
        $productEditData->heurekaCpcValues = $nullForAllDomains;
        foreach ($this->domain->getAllIds() as $domainId) {
            $productEditData->showInZboziFeed[$domainId] = true;
        }
        $productEditData->zboziCpcValues = $nullForAllDomains;
        $productEditData->zboziCpcSearchValues = $nullForAllDomains;

        return $productEditData;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return \Shopsys\ShopBundle\Model\Product\ProductEditData
     */
    public function createFromProduct(Product $product) {
        $productEditData = $this->createDefault();

        $productEditData->productData = $this->productDataFactory->createFromProduct($product);
        $productEditData->parameters = $this->getParametersData($product);
        try {
            $productEditData->manualInputPrices = $this->productInputPriceFacade->getManualInputPricesData($product);
        } catch (\Shopsys\ShopBundle\Model\Product\Pricing\Exception\MainVariantPriceCalculationException $ex) {
            $productEditData->manualInputPrices = null;
        }
        $productEditData->accessories = $this->getAccessoriesData($product);
        $productEditData->imagePositions = $this->imageFacade->getImagesByEntityIndexedById($product, null);
        $productEditData->variants = $product->getVariants();

        $this->setMultidomainData($product, $productEditData);

        return $productEditData;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return \Shopsys\ShopBundle\Model\Product\Product[position]
     */
    private function getAccessoriesData(Product $product) {
        $productAccessories = [];
        foreach ($this->productAccessoryRepository->getAllByProduct($product) as $productAccessory) {
            $productAccessories[$productAccessory->getPosition()] = $productAccessory->getAccessory();
        }

        return $productAccessories;
    }

    /**
     * @param Product $product
     * @return \Shopsys\ShopBundle\Model\Product\Parameter\ProductParameterValueData[]
     */
    private function getParametersData(Product $product) {
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
    private function setMultidomainData(Product $product, ProductEditData $productEditData) {
        $productDomains = $this->productRepository->getProductDomainsByProductIndexedByDomainId($product);
        foreach ($productDomains as $productDomain) {
            $domainId = $productDomain->getDomainId();

            $productEditData->seoTitles[$domainId] = $productDomain->getSeoTitle();
            $productEditData->seoMetaDescriptions[$domainId] = $productDomain->getSeoMetaDescription();
            $productEditData->descriptions[$domainId] = $productDomain->getDescription();
            $productEditData->shortDescriptions[$domainId] = $productDomain->getShortDescription();

            $productEditData->urls->mainOnDomains[$domainId] =
                $this->friendlyUrlFacade->findMainFriendlyUrl($domainId, 'front_product_detail', $product->getId());
            $productEditData->heurekaCpcValues[$domainId] = $productDomain->getHeurekaCpc();
            $productEditData->showInZboziFeed[$domainId] = $productDomain->getShowInZboziFeed();
            $productEditData->zboziCpcValues[$domainId] = $productDomain->getZboziCpc();
            $productEditData->zboziCpcSearchValues[$domainId] = $productDomain->getZboziCpcSearch();
        }
    }

    /**
     * @return array
     */
    private function getNullForAllDomains() {
        $nullForAllDomains = [];
        foreach ($this->domain->getAll() as $domainConfig) {
            $nullForAllDomains[$domainConfig->getId()] = null;
        }

        return $nullForAllDomains;
    }
}
