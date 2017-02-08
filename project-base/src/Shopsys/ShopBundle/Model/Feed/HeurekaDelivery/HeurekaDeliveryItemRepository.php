<?php

namespace SS6\ShopBundle\Model\Feed\HeurekaDelivery;

use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Feed\FeedItemRepositoryInterface;
use SS6\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItemFactory;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use SS6\ShopBundle\Model\Product\ProductRepository;

class HeurekaDeliveryItemRepository implements FeedItemRepositoryInterface {

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
	 * @inheritdoc
	 */
	public function getItems(DomainConfig $domainConfig, $seekItemId, $maxResults) {
		$defaultPricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainConfig->getId());
		$queryBuilder = $this->productRepository->getAllSellableUsingStockInStockQueryBuilder(
			$domainConfig->getId(),
			$defaultPricingGroup
		);
		$queryBuilder
			->orderBy('p.id', 'asc')
			->setMaxResults($maxResults);

		if ($seekItemId !== null) {
			$queryBuilder->andWhere('p.id > :seekItemId')->setParameter('seekItemId', $seekItemId);
		}

		$products = $queryBuilder->getQuery()->execute();

		return $this->heurekaDeliveryItemFactory->createItems($products, $domainConfig);
	}

}
