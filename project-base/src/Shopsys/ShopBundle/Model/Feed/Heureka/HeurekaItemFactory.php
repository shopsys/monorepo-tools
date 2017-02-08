<?php

namespace SS6\ShopBundle\Model\Feed\Heureka;

use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Category\CategoryFacade;
use SS6\ShopBundle\Model\Feed\FeedItemFactoryInterface;
use SS6\ShopBundle\Model\Feed\Heureka\HeurekaItem;
use SS6\ShopBundle\Model\Product\Collection\ProductCollectionFacade;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use SS6\ShopBundle\Model\Product\Product;

class HeurekaItemFactory implements FeedItemFactoryInterface {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser
	 */
	private $productPriceCalculationForUser;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Collection\ProductCollectionFacade
	 */
	private $productCollectionFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryFacade
	 */
	private $categoryFacade;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculationForUser
	 * @param \SS6\ShopBundle\Model\Product\Collection\ProductCollectionFacade $productCollectionFacade
	 * @param \SS6\ShopBundle\Model\Category\CategoryFacade $categoryFacade
	 */
	public function __construct(
		ProductPriceCalculationForUser $productPriceCalculationForUser,
		ProductCollectionFacade $productCollectionFacade,
		CategoryFacade $categoryFacade
	) {
		$this->productPriceCalculationForUser = $productPriceCalculationForUser;
		$this->productCollectionFacade = $productCollectionFacade;
		$this->categoryFacade = $categoryFacade;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @return \SS6\ShopBundle\Model\Feed\Heureka\HeurekaItem[]
	 */
	public function createItems(array $products, DomainConfig $domainConfig) {
		$productDomainsByProductId = $this->productCollectionFacade->getProductDomainsIndexedByProductId(
			$products,
			$domainConfig
		);
		$imagesByProductId = $this->productCollectionFacade->getImagesUrlsIndexedByProductId($products, $domainConfig);
		$urlsByProductId = $this->productCollectionFacade->getAbsoluteUrlsIndexedByProductId($products, $domainConfig);
		$paramsByProductId = $this->productCollectionFacade->getProductParameterValuesIndexedByProductIdAndParameterName(
			$products,
			$domainConfig
		);

		$items = [];
		foreach ($products as $product) {
			$productPrice = $this->productPriceCalculationForUser->calculatePriceForUserAndDomainId(
				$product,
				$domainConfig->getId(),
				null
			);
			$manufacturer = null;
			if ($product->getBrand() !== null) {
				$manufacturer = $product->getBrand()->getName();
			}
			if (array_key_exists($product->getId(), $paramsByProductId)) {
				$params = $paramsByProductId[$product->getId()];
			} else {
				$params = [];
			}
			if ($product->isVariant()) {
				$groupId = $product->getMainVariant()->getId();
			} else {
				$groupId = null;
			}

			$items[] = new HeurekaItem(
				$product->getId(),
				$product->getName($domainConfig->getLocale()),
				$productDomainsByProductId[$product->getId()]->getDescription(),
				$urlsByProductId[$product->getId()],
				$imagesByProductId[$product->getId()],
				$productPrice->getPriceWithVat(),
				$product->getEan(),
				$product->getCalculatedAvailability()->getDispatchTime(),
				$manufacturer,
				$this->getProductCategorytext($product, $domainConfig),
				$params,
				$productDomainsByProductId[$product->getId()]->getHeurekaCpc(),
				$groupId
			);
		}

		return $items;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @return string|null
	 */
	private function getProductCategorytext(Product $product, DomainConfig $domainConfig) {
		$productMainCategory = $this->categoryFacade->getProductMainCategoryByDomainId($product, $domainConfig->getId());
		$feedCategory = $productMainCategory->getHeurekaCzFeedCategory();

		if ($feedCategory !== null) {
			return $feedCategory->getFullName();
		} else {
			return null;
		}
	}

}
