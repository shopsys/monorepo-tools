<?php

namespace SS6\ShopBundle\Model\Feed\Heureka;

use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Feed\FeedItemRepositoryInterface;
use SS6\ShopBundle\Model\Feed\Heureka\HeurekaItemFactory;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use SS6\ShopBundle\Model\Product\ProductRepository;

class HeurekaItemRepository implements FeedItemRepositoryInterface {

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
	 */
	private $pricingGroupSettingFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\Heureka\HeurekaItemFactory
	 */
	private $heurekaItemFactory;

	public function __construct(
		ProductRepository $productRepository,
		PricingGroupSettingFacade $pricingGroupSettingFacade,
		HeurekaItemFactory $heurekaItemFactory
	) {
		$this->productRepository = $productRepository;
		$this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
		$this->heurekaItemFactory = $heurekaItemFactory;
	}

	/**
	 * @inheritdoc
	 */
	public function getItems(DomainConfig $domainConfig, $seekItemId, $maxResults) {
		$defaultPricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainConfig->getId());
		$queryBuilder = $this->productRepository->getAllSellableQueryBuilder($domainConfig->getId(), $defaultPricingGroup);
		$this->productRepository->addTranslation($queryBuilder, $domainConfig->getLocale());
		$queryBuilder
			->addSelect('v')->join('p.vat', 'v')
			->addSelect('a')->join('p.calculatedAvailability', 'a')
			->addSelect('b')->leftJoin('p.brand', 'b')
			->orderBy('p.id', 'asc')
			->setMaxResults($maxResults);

		if ($seekItemId !== null) {
			$queryBuilder->andWhere('p.id > :seekItemId')->setParameter('seekItemId', $seekItemId);
		}

		$products = $queryBuilder->getQuery()->execute();

		return $this->heurekaItemFactory->createItems($products, $domainConfig);
	}

}
