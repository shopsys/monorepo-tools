<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Model\Category\CategoryFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade;

class HeurekaFeedItemFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser
     */
    protected $productPriceCalculationForUser;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem\HeurekaProductDataBatchLoader
     */
    protected $productDataBatchLoader;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade
     */
    protected $heurekaCategoryFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryFacade
     */
    protected $categoryFacade;

    /**
     * @var string[]|null[]
     */
    protected $heurekaCategoryFullNamesCache = [];

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculationForUser
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem\HeurekaProductDataBatchLoader $heurekaProductDataBatchLoader
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade $heurekaCategoryFacade
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryFacade $categoryFacade
     */
    public function __construct(
        ProductPriceCalculationForUser $productPriceCalculationForUser,
        HeurekaProductDataBatchLoader $heurekaProductDataBatchLoader,
        HeurekaCategoryFacade $heurekaCategoryFacade,
        CategoryFacade $categoryFacade
    ) {
        $this->productPriceCalculationForUser = $productPriceCalculationForUser;
        $this->productDataBatchLoader = $heurekaProductDataBatchLoader;
        $this->heurekaCategoryFacade = $heurekaCategoryFacade;
        $this->categoryFacade = $categoryFacade;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem\HeurekaFeedItem
     */
    public function create(Product $product, DomainConfig $domainConfig): HeurekaFeedItem
    {
        $mainVariantId = $product->isVariant() ? $product->getMainVariant()->getId() : null;

        return new HeurekaFeedItem(
            $product->getId(),
            $mainVariantId,
            $product->getName($domainConfig->getLocale()),
            $product->getDescription($domainConfig->getId()),
            $this->productDataBatchLoader->getProductUrl($product, $domainConfig),
            $this->productDataBatchLoader->getProductImageUrl($product, $domainConfig),
            $this->getBrandName($product),
            $product->getEan(),
            $product->getCalculatedAvailability()->getDispatchTime(),
            $this->getPrice($product, $domainConfig),
            $this->getHeurekaCategoryFullName($product, $domainConfig),
            $this->productDataBatchLoader->getProductParametersByName($product, $domainConfig),
            $this->productDataBatchLoader->getProductCpc($product, $domainConfig)
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return string|null
     */
    protected function getBrandName(Product $product): ?string
    {
        $brand = $product->getBrand();

        return $brand !== null ? $brand->getName() : null;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    protected function getPrice(Product $product, DomainConfig $domainConfig): Price
    {
        return $this->productPriceCalculationForUser->calculatePriceForUserAndDomainId(
            $product,
            $domainConfig->getId(),
            null
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string|null
     */
    protected function getHeurekaCategoryFullName(Product $product, DomainConfig $domainConfig): ?string
    {
        $mainCategory = $this->categoryFacade->findProductMainCategoryByDomainId($product, $domainConfig->getId());

        if ($mainCategory !== null) {
            return $this->findHeurekaCategoryFullNameByCategoryIdUsingCache($mainCategory->getId());
        } else {
            return null;
        }
    }

    /**
     * @param int $categoryId
     * @return string|null
     */
    protected function findHeurekaCategoryFullNameByCategoryIdUsingCache(int $categoryId): ?string
    {
        if (!array_key_exists($categoryId, $this->heurekaCategoryFullNamesCache)) {
            $this->heurekaCategoryFullNamesCache[$categoryId] = $this->findHeurekaCategoryFullNameByCategoryId($categoryId);
        }

        return $this->heurekaCategoryFullNamesCache[$categoryId];
    }

    /**
     * @param int $categoryId
     * @return string|null
     */
    protected function findHeurekaCategoryFullNameByCategoryId(int $categoryId): ?string
    {
        $heurekaCategory = $this->heurekaCategoryFacade->findByCategoryId($categoryId);

        return $heurekaCategory !== null ? $heurekaCategory->getFullName() : null;
    }
}
