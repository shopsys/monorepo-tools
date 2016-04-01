<?php

namespace SS6\ShopBundle\Model\Feed\Zbozi;

use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Feed\FeedItemIterator;
use SS6\ShopBundle\Model\Feed\FeedItemIteratorFactoryInterface;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use SS6\ShopBundle\Model\Product\ProductRepository;

class ZboziItemIteratorFactory implements FeedItemIteratorFactoryInterface {

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
	 */
	private $pricingGroupSettingFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\Zbozi\ZboziItemFactory
	 */
	private $zboziItemFactory;

	public function __construct(
		ProductRepository $productRepository,
		PricingGroupSettingFacade $pricingGroupSettingFacade,
		ZboziItemFactory $zboziItemFactory
	) {
		$this->productRepository = $productRepository;
		$this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
		$this->zboziItemFactory = $zboziItemFactory;
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

		return new FeedItemIterator($queryBuilder, $this->zboziItemFactory, $domainConfig);
	}
}
