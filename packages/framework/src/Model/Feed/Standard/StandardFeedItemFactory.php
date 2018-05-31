<?php

namespace Shopsys\FrameworkBundle\Model\Feed\Standard;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Model\Category\CategoryFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\FrameworkBundle\Model\Product\Collection\ProductCollectionFacade;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\FrameworkBundle\Model\Product\Product;

class StandardFeedItemFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser
     */
    private $productPriceCalculationForUser;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Collection\ProductCollectionFacade
     */
    private $productCollectionFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryFacade
     */
    private $categoryFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculationForUser
     * @param \Shopsys\FrameworkBundle\Model\Product\Collection\ProductCollectionFacade $productCollectionFacade
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryFacade $categoryFacade
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade $currencyFacade
     */
    public function __construct(
        ProductPriceCalculationForUser $productPriceCalculationForUser,
        ProductCollectionFacade $productCollectionFacade,
        CategoryFacade $categoryFacade,
        CurrencyFacade $currencyFacade
    ) {
        $this->productPriceCalculationForUser = $productPriceCalculationForUser;
        $this->productCollectionFacade = $productCollectionFacade;
        $this->categoryFacade = $categoryFacade;
        $this->currencyFacade = $currencyFacade;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $products
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\FrameworkBundle\Model\Feed\Standard\StandardFeedItem[]
     */
    public function createItems(array $products, DomainConfig $domainConfig)
    {
        $imagesByProductId = $this->productCollectionFacade->getImagesUrlsIndexedByProductId($products, $domainConfig);
        $urlsByProductId = $this->productCollectionFacade->getAbsoluteUrlsIndexedByProductId($products, $domainConfig);
        $paramsByProductIdAndName = $this->productCollectionFacade->getProductParameterValuesIndexedByProductIdAndParameterName(
            $products,
            $domainConfig
        );
        $currency = $this->currencyFacade->getDomainDefaultCurrencyByDomainId($domainConfig->getId());

        $items = [];
        foreach ($products as $product) {
            $mainCategory = $this->categoryFacade->getProductMainCategoryByDomainId($product, $domainConfig->getId());

            $items[] = new StandardFeedItem(
                $product->getId(),
                $product->getName($domainConfig->getLocale()),
                $product->getDescription($domainConfig->getId()),
                $urlsByProductId[$product->getId()],
                $imagesByProductId[$product->getId()],
                $this->getProductPrice($product, $domainConfig->getId())->getPriceWithVat(),
                $currency->getCode(),
                $product->getEan(),
                $product->getCalculatedAvailability()->getDispatchTime(),
                $this->getProductManufacturer($product),
                $this->getProductCategoryText($product, $domainConfig),
                $this->getProductParamsIndexedByParamName($product, $paramsByProductIdAndName),
                $product->getPartno(),
                $this->findProductMainVariantId($product),
                $product->isSellingDenied(),
                $mainCategory->getId()
            );
        }

        return $items;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string|null
     */
    private function getProductCategoryText(Product $product, DomainConfig $domainConfig)
    {
        $pathFromRootCategoryToMainCategory = $this->categoryFacade->getCategoryNamesInPathFromRootToProductMainCategoryOnDomain(
            $product,
            $domainConfig
        );

        return implode(' | ', $pathFromRootCategoryToMainCategory);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return string|null
     */
    private function getProductManufacturer(Product $product)
    {
        $manufacturer = null;
        if ($product->getBrand() !== null) {
            $manufacturer = $product->getBrand()->getName();
        }

        return $manufacturer;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param string[][] $paramsByProductIdAndName
     * @return string[]
     */
    private function getProductParamsIndexedByParamName(Product $product, $paramsByProductIdAndName)
    {
        if (array_key_exists($product->getId(), $paramsByProductIdAndName)) {
            $paramsByName = $paramsByProductIdAndName[$product->getId()];
        } else {
            $paramsByName = [];
        }

        return $paramsByName;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int
     * @return \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice
     */
    private function getProductPrice(Product $product, $domainId)
    {
        return $this->productPriceCalculationForUser->calculatePriceForUserAndDomainId(
            $product,
            $domainId,
            null
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return int|null
     */
    private function findProductMainVariantId(Product $product)
    {
        if ($product->isVariant()) {
            $mainVariant = $product->getMainVariant();
            return $mainVariant->getId();
        }

        return null;
    }
}
