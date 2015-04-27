<?php

namespace SS6\ShopBundle\Model\Feed\Heureka;

use Doctrine\ORM\Internal\Hydration\IterableResult;
use Iterator;
use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Image\ImageFacade;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use SS6\ShopBundle\Model\Product\Product;
use Symfony\Component\Routing\RouterInterface;

class HeurekaDataIterator implements Iterator {

	/**
	 * @var \Doctrine\ORM\Internal\Hydration\IterableResult
	 */
	private $iterableResult;

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
	 * @var \SS6\ShopBundle\Model\Image\ImageFacade
	 */
	private $imageFacade;

	/**
	 * @param \Doctrine\ORM\Internal\Hydration\IterableResult $iterableResult
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @param \SS6\ShopBundle\Component\Router\DomainRouterFactory $domainRouterFactory
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculationForUser
	 * @param \SS6\ShopBundle\Model\Image\ImageFacade $imageFacade
	 */
	public function __construct(
		IterableResult $iterableResult,
		DomainConfig $domainConfig,
		DomainRouterFactory $domainRouterFactory,
		ProductPriceCalculationForUser $productPriceCalculationForUser,
		ImageFacade $imageFacade
	) {
		$this->iterableResult = $iterableResult;
		$this->domainConfig = $domainConfig;
		$this->productPriceCalculationForUser = $productPriceCalculationForUser;
		$this->router = $domainRouterFactory->getRouter($domainConfig->getId());
		$this->imageFacade = $imageFacade;
	}

	public function rewind() {
		$this->iterableResult->rewind();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Feed\Heureka\HeurekaItem
	 */
	public function next() {
		$current = $this->iterableResult->next();
		if ($current === false) {
			return false;
		}

		return $this->createHeurekaItem($current[0]);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Feed\Heureka\HeurekaItem
	 */
	public function current() {
		$current = $this->iterableResult->current();
		if ($current === false) {
			return false;
		}

		return $this->createHeurekaItem($current[0]);
	}

	/**
	 * @return int
	 */
	public function key() {
		return $this->iterableResult->key();
	}

	/**
	 * @return bool
	 */
	public function valid() {
		return $this->iterableResult->valid();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Feed\Heureka\HeurekaItem
	 */
	private function createHeurekaItem(Product $product) {
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

		try {
			$imageUrl = $this->imageFacade->getImageUrl($this->domainConfig, $product);
		} catch (\SS6\ShopBundle\Model\Image\Exception\ImageNotFoundException $e) {
			$imageUrl = null;
		}

		return new HeurekaItem(
			$product->getId(),
			$product->getName($this->domainConfig->getLocale()),
			$product->getDescription($this->domainConfig->getLocale()),
			$this->router->generate('front_product_detail', ['id' => $product->getId()], RouterInterface::ABSOLUTE_URL),
			$imageUrl,
			$productPrice->getPriceWithVat(),
			$product->getEan(),
			$deliveryDate
		);
	}

}
