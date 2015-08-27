<?php

namespace SS6\ShopBundle\Model\Feed\Heureka;

use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Model\Category\CategoryFacade;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Feed\AbstractDataIterator;
use SS6\ShopBundle\Model\Feed\Heureka\HeurekaItem;
use SS6\ShopBundle\Model\Product\Collection\ProductCollectionFacade;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use SS6\ShopBundle\Model\Product\Product;

class HeurekaDataIterator extends AbstractDataIterator {

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Config\DomainConfig
	 */
	private $domainConfig;

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
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculationForUser
	 * @param \SS6\ShopBundle\Model\Product\Collection\ProductCollectionFacade $productCollectionFacade
	 */
	public function __construct(
		QueryBuilder $queryBuilder,
		DomainConfig $domainConfig,
		ProductPriceCalculationForUser $productPriceCalculationForUser,
		ProductCollectionFacade $productCollectionFacade,
		CategoryFacade $categoryFacade
	) {
		$this->domainConfig = $domainConfig;
		$this->productPriceCalculationForUser = $productPriceCalculationForUser;
		$this->productCollectionFacade = $productCollectionFacade;
		$this->categoryFacade = $categoryFacade;

		parent::__construct($queryBuilder);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @return \SS6\ShopBundle\Model\Feed\Heureka\HeurekaItem[]
	 */
	protected function createItems(array $products) {
		$productDomainsByProductId = $this->productCollectionFacade->getProductDomainsIndexedByProductId(
			$products,
			$this->domainConfig
		);
		$imagesByProductId = $this->productCollectionFacade->findImagesUrlsIndexedByProductId($products, $this->domainConfig);
		$urlsByProductId = $this->productCollectionFacade->getAbsoluteUrlsIndexedByProductId($products, $this->domainConfig);
		$paramsByProductId = $this->productCollectionFacade->getProductParameterValuesIndexedByProductIdAndParameterName(
			$products,
			$this->domainConfig
		);

		$items = [];
		foreach ($products as $product) {
			$productPrice = $this->productPriceCalculationForUser->calculatePriceForUserAndDomainId(
				$product,
				$this->domainConfig->getId(),
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

			$items[] = new HeurekaItem(
				$product->getId(),
				$product->getName($this->domainConfig->getLocale()),
				$productDomainsByProductId[$product->getId()]->getDescription(),
				$urlsByProductId[$product->getId()],
				$imagesByProductId[$product->getId()],
				$productPrice->getPriceWithVat(),
				$product->getEan(),
				$product->getCalculatedAvailability()->getDispatchTime(),
				$manufacturer,
				$this->getProductCategorytext($product, $this->domainConfig),
				$params,
				$productDomainsByProductId[$product->getId()]->getHeurekaCpc()
			);
		}

		return $items;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
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
