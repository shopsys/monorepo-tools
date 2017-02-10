<?php

namespace Shopsys\ShopBundle\Model\Feed\Zbozi;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Model\Category\CategoryFacade;
use Shopsys\ShopBundle\Model\Feed\FeedItemFactoryInterface;
use Shopsys\ShopBundle\Model\Product\Collection\ProductCollectionFacade;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\ShopBundle\Model\Product\Product;

class ZboziItemFactory implements FeedItemFactoryInterface {

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser
	 */
	private $productPriceCalculationForUser;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\Collection\ProductCollectionFacade
	 */
	private $productCollectionFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Category\CategoryFacade
	 */
	private $categoryFacade;

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculationForUser
	 * @param \Shopsys\ShopBundle\Model\Product\Collection\ProductCollectionFacade $productCollectionFacade
	 * @param \Shopsys\ShopBundle\Model\Category\CategoryFacade $categoryFacade
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
	 * @param \Shopsys\ShopBundle\Model\Product\Product[] $products
	 * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @return \Shopsys\ShopBundle\Model\Feed\Heureka\HeurekaItem[]
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
			$productDomain = $productDomainsByProductId[$product->getId()];

			$items[] = new ZboziItem(
				$product->getId(),
				$product->getName($domainConfig->getLocale()),
				$productDomain->getDescription(),
				$urlsByProductId[$product->getId()],
				$imagesByProductId[$product->getId()],
				$this->getProductPrice($product, $domainConfig->getId())->getPriceWithVat(),
				$product->getEan(),
				$this->getProductDeliveryDate($product),
				$this->getProductManufacturer($product),
				$this->getProductCategoryText($product, $domainConfig),
				$this->getProductParams($product, $paramsByProductId),
				$product->getPartno(),
				$productDomain->getZboziCpc(),
				$productDomain->getZboziCpcSearch()
			);
		}

		return $items;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @return string|null
	 */
	private function getProductCategoryText(Product $product, DomainConfig $domainConfig) {
		$pathFromRootCategoryToMainCategory = $this->categoryFacade->getCategoryNamesInPathFromRootToProductMainCategoryOnDomain(
			$product,
			$domainConfig
		);

		return implode(' | ', $pathFromRootCategoryToMainCategory);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 * @return string|null
	 */
	private function getProductManufacturer(Product $product) {
		$manufacturer = null;
		if ($product->getBrand() !== null) {
			$manufacturer = $product->getBrand()->getName();
		}

		return $manufacturer;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 * @return string[productId][paramName] $paramsByProductId
	 * @return string[paramName]
	 */
	private function getProductParams(Product $product, $paramsByProductId) {
		if (array_key_exists($product->getId(), $paramsByProductId)) {
			$params = $paramsByProductId[$product->getId()];
		} else {
			$params = [];
		}

		return $params;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 * @return int
	 */
	private function getProductDeliveryDate(Product $product) {
		if ($product->getCalculatedAvailability()->getDispatchTime() === null) {
			$deliveryDate = -1;
		} else {
			$deliveryDate = $product->getCalculatedAvailability()->getDispatchTime();
		}

		return $deliveryDate;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 * @param int
	 * @return \Shopsys\ShopBundle\Model\Product\Pricing\ProductPrice
	 */
	private function getProductPrice(Product $product, $domainId) {
		return $this->productPriceCalculationForUser->calculatePriceForUserAndDomainId(
			$product,
			$domainId,
			null
		);
	}

}
