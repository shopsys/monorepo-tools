<?php

namespace SS6\ShopBundle\Form\Front\Product;

use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Product\Filter\FlagFilterRepository;
use SS6\ShopBundle\Model\Product\Filter\ParameterFilterChoiceRepository;

class ProductFilterFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Filter\ParameterFilterChoiceRepository
	 */
	private $parameterFilterChoiceRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Filter\FlagFilterRepository
	 */
	private $flagFilterRepository;

	public function __construct(
		ParameterFilterChoiceRepository $parameterFilterChoiceRepository,
		FlagFilterRepository $flagFilterRepository
	) {
		$this->parameterFilterChoiceRepository = $parameterFilterChoiceRepository;
		$this->flagFilterRepository = $flagFilterRepository;
	}

	/**
	 * @param int $domainId
	 * @param string $locale
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @return \SS6\ShopBundle\Form\Front\Product\ProductFilterFormType
	 */
	public function create($domainId, $locale, Category $category) {
		$parameterFilterChoices = $this->parameterFilterChoiceRepository
			->getParameterFilterChoicesInCategory($domainId, $locale, $category);
		$flagFilterChoices = $this->flagFilterRepository
			->getFlagFilterChoicesInCategory($domainId, $category);

		return new ProductFilterFormType($parameterFilterChoices, $flagFilterChoices);
	}

}
