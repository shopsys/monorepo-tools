<?php

namespace SS6\ShopBundle\Model\Feed\HeurekaDelivery;

use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Feed\FeedItemIterator;
use SS6\ShopBundle\Model\Feed\FeedItemIteratorFactoryInterface;
use SS6\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItemFactory;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use SS6\ShopBundle\Model\Product\ProductRepository;

class HeurekaDeliveryItemIteratorFactory implements FeedItemIteratorFactoryInterface {

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
	 */
	private $pricingGroupSettingFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItemFactory
	 */
	private $heurekaDeliveryItemFactory;

	public function __construct(
		ProductRepository $productRepository,
		PricingGroupSettingFacade $pricingGroupSettingFacade,
		HeurekaDeliveryItemFactory $heurekaDeliveryItemFactory
	) {
		$this->productRepository = $productRepository;
		$this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
		$this->heurekaDeliveryItemFactory = $heurekaDeliveryItemFactory;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getIterator(DomainConfig $domainConfig) {
		$defaultPricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainConfig->getId());
		$queryBuilder = $this->productRepository->getAllSellableUsingStockInStockQueryBuilder(
			$domainConfig->getId(),
			$defaultPricingGroup
		);

		return new FeedItemIterator($queryBuilder, $this->heurekaDeliveryItemFactory, $domainConfig);
	}
}
