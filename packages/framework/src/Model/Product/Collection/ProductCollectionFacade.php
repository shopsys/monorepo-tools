<?php

namespace Shopsys\FrameworkBundle\Model\Product\Collection;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig;
use Shopsys\FrameworkBundle\Component\Image\ImageFacade;
use Shopsys\FrameworkBundle\Component\Image\ImageRepository;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlService;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterRepository;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;

class ProductCollectionFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Collection\ProductCollectionService
     */
    protected $productCollectionService;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig
     */
    protected $imageConfig;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageRepository
     */
    protected $imageRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageFacade
     */
    protected $imageFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository
     */
    protected $friendlyUrlRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlService
     */
    protected $friendlyUrlService;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterRepository
     */
    protected $parameterRepository;

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
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $products
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
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
                } catch (\Shopsys\FrameworkBundle\Component\Image\Exception\ImageNotFoundException $e) {
                    $imagesUrlsByProductId[$productId] = null;
                }
            }
        }

        return $imagesUrlsByProductId;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $products
     * @return \Shopsys\FrameworkBundle\Component\Image\Image[]
     */
    protected function getMainImagesIndexedByProductId(array $products)
    {
        $productEntityName = $this->imageConfig->getImageEntityConfigByClass(Product::class)->getEntityName();
        $imagesByProductId = $this->imageRepository->getMainImagesByEntitiesIndexedByEntityId($products, $productEntityName);

        return $this->productCollectionService->getImagesIndexedByProductId($products, $imagesByProductId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $products
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
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
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $products
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string[][]
     */
    public function getProductParameterValuesIndexedByProductIdAndParameterName(array $products, DomainConfig $domainConfig)
    {
        $locale = $domainConfig->getLocale();

        return $this->parameterRepository->getParameterValuesIndexedByProductIdAndParameterNameForProducts($products, $locale);
    }
}
