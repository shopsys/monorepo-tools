<?php

namespace SS6\ShopBundle\Form\Front\Product;

use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Product\Filter\BrandFilterChoiceRepository;
use SS6\ShopBundle\Model\Product\Filter\FlagFilterChoiceRepository;
use SS6\ShopBundle\Model\Product\Filter\ParameterFilterChoiceRepository;

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

	public function __construct(
		ParameterFilterChoiceRepository $parameterFilterChoiceRepository,
		FlagFilterChoiceRepository $flagFilterChoiceRepository,
		CurrentCustomer $currentCustomer,
		BrandFilterChoiceRepository $brandFilterChoiceRepository
	) {
		$this->parameterFilterChoiceRepository = $parameterFilterChoiceRepository;
		$this->flagFilterChoiceRepository = $flagFilterChoiceRepository;
		$this->currentCustomer = $currentCustomer;
		$this->brandFilterChoiceRepository = $brandFilterChoiceRepository;
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

		return new ProductFilterFormType($parameterFilterChoices, $flagFilterChoices, $brandFilterChoices);
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

		return new ProductFilterFormType($parameterFilterChoices, $flagFilterChoices, $brandFilterChoices);
	}

}
