<?php

namespace SS6\ShopBundle\Model\Product\Collection;

use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlService;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Image\Config\ImageConfig;
use SS6\ShopBundle\Model\Image\ImageFacade;
use SS6\ShopBundle\Model\Image\ImageRepository;
use SS6\ShopBundle\Model\Product\Collection\ProductCollectionService;
use SS6\ShopBundle\Model\Product\Product;

class ProductCollectionFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Collection\ProductCollectionService
	 */
	private $productCollectionService;

	/**
	 * @var \SS6\ShopBundle\Model\Image\Config\ImageConfig
	 */
	private $imageConfig;

	/**
	 * @var \SS6\ShopBundle\Model\Image\ImageRepository
	 */
	private $imageRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Image\ImageFacade
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

	public function __construct(
		ProductCollectionService $productCollectionService,
		ImageConfig $imageConfig,
		ImageRepository $imageRepository,
		ImageFacade $imageFacade,
		FriendlyUrlRepository $friendlyUrlRepository,
		FriendlyUrlService $friendlyUrlService
	) {
		$this->productCollectionService = $productCollectionService;
		$this->imageConfig = $imageConfig;
		$this->imageRepository = $imageRepository;
		$this->imageFacade = $imageFacade;
		$this->friendlyUrlRepository = $friendlyUrlRepository;
		$this->friendlyUrlService = $friendlyUrlService;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @param string|null $sizeName
	 * @return \SS6\ShopBundle\Model\Image\Image[productId]
	 */
	public function findImagesUrlIndexedByProductId(array $products, DomainConfig $domainConfig, $sizeName = null) {
		$imagesUrlByProductId = [];
		foreach ($this->findMainImagesIndexedByProductId($products) as $productId => $image) {
			if ($image === null) {
				$imagesUrlByProductId[$productId] = null;
			} else {
				try {
					$imagesUrlByProductId[$productId] = $this->imageFacade->getImageUrl($domainConfig, $image, $sizeName);
				} catch (\SS6\ShopBundle\Model\Image\Exception\ImageNotFoundException $e) {
					$imagesUrlByProductId[$productId] = null;
				}
			}
		}

		return $imagesUrlByProductId;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @return \SS6\ShopBundle\Model\Image\Image[productId]
	 */
	private function findMainImagesIndexedByProductId(array $products) {
		$productEntityName = $this->imageConfig->getImageEntityConfigByClass(Product::class)->getEntityName();
		$imagesByProductId = $this->imageRepository->getMainImagesByEntitiesIndexedByEntityId($products, $productEntityName);

		return $this->productCollectionService->getImagesIndexedByProductId($products, $imagesByProductId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @return \SS6\ShopBundle\Model\Image\Image[productId]
	 */
	public function getAbsoluteUrlsIndexedByProductId(array $products, DomainConfig $domainConfig) {
		$mainFriendlyUrlByProductid = $this->friendlyUrlRepository->getMainFriendlyUrlsByEntitiesIndexedByEntityId(
			$products,
			'front_product_detail',
			$domainConfig->getId()
		);

		$absoluteUrlsByProductId = [];
		foreach ($mainFriendlyUrlByProductid as $productId => $friendlyUrl) {
			$absoluteUrlsByProductId[$productId] = $this->friendlyUrlService->getAbsoluteUrlByFriendlyUrl($friendlyUrl);
		}

		return $absoluteUrlsByProductId;
	}
}
