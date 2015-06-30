<?php

namespace SS6\ShopBundle\Model\Feed\Heureka;

use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Feed\AbstractDataIterator;
use SS6\ShopBundle\Model\Feed\Heureka\HeurekaItem;
use SS6\ShopBundle\Model\Product\Collection\ProductCollectionFacade;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;

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
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculationForUser
	 * @param \SS6\ShopBundle\Model\Product\Collection\ProductCollectionFacade $productCollectionFacade
	 */
	public function __construct(
		QueryBuilder $queryBuilder,
		DomainConfig $domainConfig,
		ProductPriceCalculationForUser $productPriceCalculationForUser,
		ProductCollectionFacade $productCollectionFacade
	) {
		$this->domainConfig = $domainConfig;
		$this->productPriceCalculationForUser = $productPriceCalculationForUser;
		$this->productCollectionFacade = $productCollectionFacade;

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

		$items = [];
		foreach ($products as $product) {
			$calculatedAvailability = $product->getCalculatedAvailability();
			if ($calculatedAvailability === null) {
				$deliveryDate = null;
			} else {
				$deliveryDate = $calculatedAvailability->getDeliveryTime();
			}

			$productPrice = $this->productPriceCalculationForUser->calculatePriceForUserAndDomainId(
				$product,
				$this->domainConfig->getId(),
				null
			);

			$items[] = new HeurekaItem(
				$product->getId(),
				$product->getName($this->domainConfig->getLocale()),
				$productDomainsByProductId[$product->getId()]->getDescription(),
				$urlsByProductId[$product->getId()],
				$imagesByProductId[$product->getId()],
				$productPrice->getPriceWithVat(),
				$product->getEan(),
				$deliveryDate
			);
		}

		return $items;
	}

}
