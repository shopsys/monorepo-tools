<?php

namespace Shopsys\ShopBundle\Model\Product\Collection;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Image\Config\ImageConfig;
use Shopsys\ShopBundle\Component\Image\ImageFacade;
use Shopsys\ShopBundle\Component\Image\ImageRepository;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlService;
use Shopsys\ShopBundle\Model\Product\Collection\ProductCollectionService;
use Shopsys\ShopBundle\Model\Product\Parameter\ParameterRepository;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductRepository;

class ProductCollectionFacade
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\Collection\ProductCollectionService
     */
    private $productCollectionService;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\Config\ImageConfig
     */
    private $imageConfig;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\ImageRepository
     */
    private $imageRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\ImageFacade
     */
    private $imageFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository
     */
    private $friendlyUrlRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlService
     */
    private $friendlyUrlService;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Parameter\ParameterRepository
     */
    private $parameterRepository;

    public function __construct(
        ProductCollectionService $productCollectionService,
        ProductRepository $productRepository,
        ImageConfig $imageConfig,
        ImageRepository $imageRepository,
        ImageFacade $imageFacade,
        FriendlyUrlRepository $friendlyUrlRepository,
        FriendlyUrlService $friendlyUrlService,
        ParameterRepository $parameterRepository
    ) {
        $this->productCollectionService = $productCollectionService;
        $this->imageConfig = $imageConfig;
        $this->imageRepository = $imageRepository;
        $this->imageFacade = $imageFacade;
        $this->friendlyUrlRepository = $friendlyUrlRepository;
        $this->friendlyUrlService = $friendlyUrlService;
        $this->productRepository = $productRepository;
        $this->parameterRepository = $parameterRepository;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $products
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param string|null $sizeName
     * @return string[]
     */
    public function getImagesUrlsIndexedByProductId(array $products, DomainConfig $domainConfig, $sizeName = null)
    {
        $imagesUrlsByProductId = [];
        foreach ($this->getMainImagesIndexedByProductId($products) as $productId => $image) {
            if ($image === null) {
                $imagesUrlsByProductId[$productId] = null;
            } else {
                try {
                    $imagesUrlsByProductId[$productId] = $this->imageFacade->getImageUrl($domainConfig, $image, $sizeName);
                } catch (\Shopsys\ShopBundle\Component\Image\Exception\ImageNotFoundException $e) {
                    $imagesUrlsByProductId[$productId] = null;
                }
            }
        }

        return $imagesUrlsByProductId;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $products
     * @return \Shopsys\ShopBundle\Component\Image\Image[]
     */
    private function getMainImagesIndexedByProductId(array $products)
    {
        $productEntityName = $this->imageConfig->getImageEntityConfigByClass(Product::class)->getEntityName();
        $imagesByProductId = $this->imageRepository->getMainImagesByEntitiesIndexedByEntityId($products, $productEntityName);

        return $this->productCollectionService->getImagesIndexedByProductId($products, $imagesByProductId);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $products
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string[]
     */
    public function getAbsoluteUrlsIndexedByProductId(array $products, DomainConfig $domainConfig)
    {
        $mainFriendlyUrlsByProductId = $this->friendlyUrlRepository->getMainFriendlyUrlsByEntitiesIndexedByEntityId(
            $products,
            'front_product_detail',
            $domainConfig->getId()
        );

        $absoluteUrlsByProductId = [];
        foreach ($mainFriendlyUrlsByProductId as $productId => $friendlyUrl) {
            $absoluteUrlsByProductId[$productId] = $this->friendlyUrlService->getAbsoluteUrlByFriendlyUrl($friendlyUrl);
        }

        return $absoluteUrlsByProductId;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $products
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\ShopBundle\Model\Product\ProductDomain[]
     */
    public function getProductDomainsIndexedByProductId(array $products, DomainConfig $domainConfig)
    {
        return $this->productRepository->getProductDomainsByProductsAndDomainIdIndexedByProductId(
            $products,
            $domainConfig->getId()
        );
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $products
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string[][]
     */
    public function getProductParameterValuesIndexedByProductIdAndParameterName(array $products, DomainConfig $domainConfig)
    {
        $locale = $domainConfig->getLocale();

        return $this->parameterRepository->getParameterValuesIndexedByProductIdAndParameterNameForProducts($products, $locale);
    }
}
