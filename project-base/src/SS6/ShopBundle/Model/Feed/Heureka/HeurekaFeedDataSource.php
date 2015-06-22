<?php

namespace SS6\ShopBundle\Model\Feed\Heureka;

use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Feed\FeedDataSourceInterface;
use SS6\ShopBundle\Model\Image\ImageFacade;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use SS6\ShopBundle\Model\Product\ProductRepository;

class HeurekaFeedDataSource implements FeedDataSourceInterface {

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
	 */
	private $pricingGroupSettingFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Router\DomainRouterFactory
	 */
	private $domainRouterFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser
	 */
	private $productPriceCalculationForUser;

	/**
	 * @var \SS6\ShopBundle\Model\Image\ImageFacade
	 */
	private $imageFacade;

	public function __construct(
		ProductRepository $productRepository,
		PricingGroupSettingFacade $pricingGroupSettingFacade,
		DomainRouterFactory $domainRouterFactory,
		ProductPriceCalculationForUser $productPriceCalculationForUser,
		ImageFacade $imageFacade
	) {
		$this->productRepository = $productRepository;
		$this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
		$this->domainRouterFactory = $domainRouterFactory;
		$this->productPriceCalculationForUser = $productPriceCalculationForUser;
		$this->imageFacade = $imageFacade;
	}

	/**
	 * @inheritDoc
	 */
	public function getIterator(DomainConfig $domainConfig) {
		$defaultPricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainConfig->getId());
		$queryBuilder = $this->productRepository->getAllListableQueryBuilder($domainConfig->getId(), $defaultPricingGroup);

		return new HeurekaDataIterator(
			$queryBuilder,
			$domainConfig,
			$this->domainRouterFactory,
			$this->productPriceCalculationForUser,
			$this->imageFacade
		);
	}
}
