<?php

namespace SS6\ShopBundle\Form\Front\Product;

use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Product\Filter\BrandFilterChoiceRepository;
use SS6\ShopBundle\Model\Product\Filter\FlagFilterChoiceRepository;
use SS6\ShopBundle\Model\Product\Filter\ParameterFilterChoiceRepository;
use SS6\ShopBundle\Model\Product\Filter\PriceRangeRepository;

class ProductFilterFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Filter\ParameterFilterChoiceRepository
	 */
	private $parameterFilterChoiceRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Filter\FlagFilterChoiceRepository
	 */
	private $flagFilterChoiceRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CurrentCustomer
	 */
	private $currentCustomer;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Filter\BrandFilterChoiceRepository
	 */
	private $brandFilterChoiceRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Filter\PriceRangeRepository
	 */
	private $priceRangeRepository;

	public function __construct(
		ParameterFilterChoiceRepository $parameterFilterChoiceRepository,
		FlagFilterChoiceRepository $flagFilterChoiceRepository,
		CurrentCustomer $currentCustomer,
		BrandFilterChoiceRepository $brandFilterChoiceRepository,
		PriceRangeRepository $priceRangeRepository
	) {
		$this->parameterFilterChoiceRepository = $parameterFilterChoiceRepository;
		$this->flagFilterChoiceRepository = $flagFilterChoiceRepository;
		$this->currentCustomer = $currentCustomer;
		$this->brandFilterChoiceRepository = $brandFilterChoiceRepository;
		$this->priceRangeRepository = $priceRangeRepository;
	}

	/**
	 * @param int $domainId
	 * @param string $locale
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @return \SS6\ShopBundle\Form\Front\Product\ProductFilterFormType
	 */
	public function createForCategory($domainId, $locale, Category $category) {
		$pricingGroup = $this->currentCustomer->getPricingGroup();
		$parameterFilterChoices = $this->parameterFilterChoiceRepository
			->getParameterFilterChoicesInCategory($domainId, $pricingGroup, $locale, $category);
		$flagFilterChoices = $this->flagFilterChoiceRepository
			->getFlagFilterChoicesInCategory($domainId, $pricingGroup, $category);
		$brandFilterChoices = $this->brandFilterChoiceRepository
			->getBrandFilterChoicesInCategory($domainId, $pricingGroup, $category);
		$priceRange = $this->priceRangeRepository->getPriceRangeInCategory($domainId, $pricingGroup, $category);

		return new ProductFilterFormType($parameterFilterChoices, $flagFilterChoices, $brandFilterChoices, $priceRange);
	}

	/**
	 * @param int $domainId
	 * @param string $locale
	 * @param string|null $searchText
	 * @return \SS6\ShopBundle\Form\Front\Product\ProductFilterFormType
	 */
	public function createForSearch($domainId, $locale, $searchText) {
		$parameterFilterChoices = [];
		$pricingGroup = $this->currentCustomer->getPricingGroup();
		$flagFilterChoices = $this->flagFilterChoiceRepository
			->getFlagFilterChoicesForSearch($domainId, $pricingGroup, $locale, $searchText);
		$brandFilterChoices = $this->brandFilterChoiceRepository
			->getBrandFilterChoicesForSearch($domainId, $pricingGroup, $locale, $searchText);
		$priceRange = $this->priceRangeRepository->getPriceRangeForSearch($domainId, $pricingGroup, $locale, $searchText);

		return new ProductFilterFormType($parameterFilterChoices, $flagFilterChoices, $brandFilterChoices, $priceRange);
	}

}
