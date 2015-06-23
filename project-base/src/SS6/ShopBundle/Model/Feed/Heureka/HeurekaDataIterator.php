<?php

namespace SS6\ShopBundle\Model\Feed\Heureka;

use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Feed\AbstractDataIterator;
use SS6\ShopBundle\Model\Product\Collection\ProductCollectionFacade;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Symfony\Component\Routing\RouterInterface;

class HeurekaDataIterator extends AbstractDataIterator {

	const KEY_PRODUCT = 'product';
	const KEY_IMAGE_URL = 'image_url';

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Config\DomainConfig
	 */
	private $domainConfig;

	/**
	 * @var \Symfony\Component\Routing\RouterInterface
	 */
	private $router;

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
	 * @param \SS6\ShopBundle\Component\Router\DomainRouterFactory $domainRouterFactory
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculationForUser
	 * @param \SS6\ShopBundle\Model\Product\Collection\ProductCollectionFacade $productCollectionFacade
	 */
	public function __construct(
		QueryBuilder $queryBuilder,
		DomainConfig $domainConfig,
		DomainRouterFactory $domainRouterFactory,
		ProductPriceCalculationForUser $productPriceCalculationForUser,
		ProductCollectionFacade $productCollectionFacade
	) {
		$this->domainConfig = $domainConfig;
		$this->productPriceCalculationForUser = $productPriceCalculationForUser;
		$this->router = $domainRouterFactory->getRouter($domainConfig->getId());
		$this->productCollectionFacade = $productCollectionFacade;

		parent::__construct($queryBuilder);
	}

	/**
	 * @param array $row
	 * @return \SS6\ShopBundle\Model\Feed\Heureka\HeurekaItem
	 */
	protected function createItem(array $row) {
		$product = $row[self::KEY_PRODUCT];
		/* @var $product \SS6\ShopBundle\Model\Product\Product */
		
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

		return new HeurekaItem(
			$product->getId(),
			$product->getName($this->domainConfig->getLocale()),
			$product->getDescription($this->domainConfig->getLocale()),
			$this->router->generate('front_product_detail', ['id' => $product->getId()], RouterInterface::ABSOLUTE_URL),
			$row[self::KEY_IMAGE_URL],
			$productPrice->getPriceWithVat(),
			$product->getEan(),
			$deliveryDate
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @return array
	 */
	protected function loadOtherData(array $products) {
		$imagesByProductId = $this->productCollectionFacade->findImagesUrlIndexedByProductId($products, $this->domainConfig);

		$result = [];
		foreach ($products as $product) {
			$result[] = [
				self::KEY_PRODUCT => $product,
				self::KEY_IMAGE_URL => $imagesByProductId[$product->getId()],
			];
		}

		return $result;
	}

}
