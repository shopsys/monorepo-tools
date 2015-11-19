<?php

namespace SS6\ShopBundle\Model\Product\Collection;

use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Component\Image\Config\ImageConfig;
use SS6\ShopBundle\Component\Image\ImageFacade;
use SS6\ShopBundle\Component\Image\ImageRepository;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlService;
use SS6\ShopBundle\Model\Product\Collection\ProductCollectionService;
use SS6\ShopBundle\Model\Product\Parameter\ParameterRepository;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductRepository;

class ProductCollectionFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Collection\ProductCollectionService
	 */
	private $productCollectionService;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Component\Image\Config\ImageConfig
	 */
	private $imageConfig;

	/**
	 * @var \SS6\ShopBundle\Component\Image\ImageRepository
	 */
	private $imageRepository;

	/**
	 * @var \SS6\ShopBundle\Component\Image\ImageFacade
	 */
	private $imageFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository
	 */
	private $friendlyUrlRepository;

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlService
	 */
	private $friendlyUrlService;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterRepository
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
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @param string|null $sizeName
	 * @return string[productId]
	 */
	public function getImagesUrlsIndexedByProductId(array $products, DomainConfig $domainConfig, $sizeName = null) {
		$imagesUrlsByProductId = [];
		foreach ($this->getMainImagesIndexedByProductId($products) as $productId => $image) {
			if ($image === null) {
				$imagesUrlsByProductId[$productId] = null;
			} else {
				try {
					$imagesUrlsByProductId[$productId] = $this->imageFacade->getImageUrl($domainConfig, $image, $sizeName);
				} catch (\SS6\ShopBundle\Component\Image\Exception\ImageNotFoundException $e) {
					$imagesUrlsByProductId[$productId] = null;
				}
			}
		}

		return $imagesUrlsByProductId;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @return \SS6\ShopBundle\Component\Image\Image[productId]
	 */
	private function getMainImagesIndexedByProductId(array $products) {
		$productEntityName = $this->imageConfig->getImageEntityConfigByClass(Product::class)->getEntityName();
		$imagesByProductId = $this->imageRepository->getMainImagesByEntitiesIndexedByEntityId($products, $productEntityName);

		return $this->productCollectionService->getImagesIndexedByProductId($products, $imagesByProductId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @return \SS6\ShopBundle\Component\Image\Image[productId]
	 */
	public function getAbsoluteUrlsIndexedByProductId(array $products, DomainConfig $domainConfig) {
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
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @return \SS6\ShopBundle\Model\Product\ProductDomain[productId]
	 */
	public function getProductDomainsIndexedByProductId(array $products, DomainConfig $domainConfig) {
		return $this->productRepository->getProductDomainsByProductsAndDomainConfigIndexedByProductId(
			$products,
			$domainConfig
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @return string[productId][paramName]
	 */
	public function getProductParameterValuesIndexedByProductIdAndParameterName(array $products, DomainConfig $domainConfig) {
		$locale = $domainConfig->getLocale();

		return $this->parameterRepository->getParameterValuesIndexedByProductIdAndParameterNameForProducts($products, $locale);
	}
}
