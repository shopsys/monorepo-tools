<?php

namespace SS6\ShopBundle\Model\Feed\Heureka;

use SS6\ShopBundle\Model\Category\CategoryFacade;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Feed\FeedDataSourceInterface;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use SS6\ShopBundle\Model\Product\Collection\ProductCollectionFacade;
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

	public function __construct(
		ProductRepository $productRepository,
		PricingGroupSettingFacade $pricingGroupSettingFacade,
		ProductPriceCalculationForUser $productPriceCalculationForUser,
		ProductCollectionFacade $productCollectionFacade,
		CategoryFacade $categoryFacade
	) {
		$this->productRepository = $productRepository;
		$this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
		$this->productPriceCalculationForUser = $productPriceCalculationForUser;
		$this->productCollectionFacade = $productCollectionFacade;
		$this->categoryFacade = $categoryFacade;
	}

	/**
	 * @inheritDoc
	 */
	public function getIterator(DomainConfig $domainConfig) {
		$defaultPricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainConfig->getId());
		$queryBuilder = $this->productRepository->getAllSellableQueryBuilder($domainConfig->getId(), $defaultPricingGroup);
		$this->productRepository->addTranslation($queryBuilder, $domainConfig->getLocale());
		$queryBuilder->addSelect('v')->join('p.vat', 'v');
		$queryBuilder->addSelect('a')->join('p.calculatedAvailability', 'a');
		$queryBuilder->addSelect('b')->leftJoin('p.brand', 'b');

		return new HeurekaDataIterator(
			$queryBuilder,
			$domainConfig,
			$this->productPriceCalculationForUser,
			$this->productCollectionFacade,
			$this->categoryFacade
		);
	}
}
