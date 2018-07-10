<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Model\Product\Collection\ProductParametersBatchLoader;
use Shopsys\FrameworkBundle\Model\Product\Collection\ProductUrlsBatchLoader;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainFacade;

class HeurekaProductDataBatchLoader
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Collection\ProductUrlsBatchLoader
     */
    protected $productUrlsBatchLoader;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Collection\ProductParametersBatchLoader
     */
    protected $productParametersBatchLoader;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainFacade
     */
    protected $heurekaProductDomainFacade;

    /**
     * @var float[]|null[]
     */
    protected $loadedProductCpcs = [];

    public function __construct(
        ProductUrlsBatchLoader $productUrlsBatchLoader,
        ProductParametersBatchLoader $productParametersBatchLoader,
        HeurekaProductDomainFacade $heurekaProductDomainFacade
    ) {
        $this->productUrlsBatchLoader = $productUrlsBatchLoader;
        $this->productParametersBatchLoader = $productParametersBatchLoader;
        $this->heurekaProductDomainFacade = $heurekaProductDomainFacade;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $products
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     */
    public function loadForProducts(array $products, DomainConfig $domainConfig): void
    {
        $this->productUrlsBatchLoader->loadForProducts($products, $domainConfig);
        $this->productParametersBatchLoader->loadForProducts($products, $domainConfig);

        $heurekaProductDomainByProductId = $this->heurekaProductDomainFacade->getHeurekaProductDomainsByProductsAndDomainIndexedByProductId($products, $domainConfig);

        foreach ($products as $product) {
            $key = $this->getKey($product, $domainConfig);
            $productId = $product->getId();

            $heurekaProductDomain = $heurekaProductDomainByProductId[$productId] ?? null;
            $this->loadedProductCpcs[$key] = $heurekaProductDomain !== null ? $heurekaProductDomain->getCpc() : null;
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return float|null
     */
    public function getProductCpc(Product $product, DomainConfig $domainConfig): ?float
    {
        $key = $this->getKey($product, $domainConfig);
        if (!array_key_exists($key, $this->loadedProductCpcs)) {
            throw new \Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem\HeurekaProductDataNotLoadedException($product, $domainConfig, 'CPC');
        }

        return $this->loadedProductCpcs[$key];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string[]
     */
    public function getProductParametersByName(Product $product, DomainConfig $domainConfig): array
    {
        try {
            return $this->productParametersBatchLoader->getProductParametersByName($product, $domainConfig);
        } catch (\Shopsys\FrameworkBundle\Model\Product\Collection\Exception\ProductParametersNotLoadedException $e) {
            throw new \Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem\HeurekaProductDataNotLoadedException($product, $domainConfig, 'parameters', $e);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string
     */
    public function getProductUrl(Product $product, DomainConfig $domainConfig): string
    {
        try {
            return $this->productUrlsBatchLoader->getProductUrl($product, $domainConfig);
        } catch (\Shopsys\FrameworkBundle\Model\Product\Collection\Exception\ProductUrlNotLoadedException $e) {
            throw new \Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem\HeurekaProductDataNotLoadedException($product, $domainConfig, 'URL', $e);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string|null
     */
    public function getProductImageUrl(Product $product, DomainConfig $domainConfig): ?string
    {
        try {
            return $this->productUrlsBatchLoader->getProductImageUrl($product, $domainConfig);
        } catch (\Shopsys\FrameworkBundle\Model\Product\Collection\Exception\ProductImageUrlNotLoadedException $e) {
            throw new \Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem\HeurekaProductDataNotLoadedException($product, $domainConfig, 'URL for image', $e);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string
     */
    protected function getKey(Product $product, DomainConfig $domainConfig): string
    {
        return $domainConfig->getId() . '-' . $product->getId();
    }
}
