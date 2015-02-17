<?php

namespace SS6\ShopBundle\Form\Front\Product;

use SS6\ShopBundle\Model\Category\Category;
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

	public function __construct(
		ParameterFilterChoiceRepository $parameterFilterChoiceRepository,
		FlagFilterChoiceRepository $flagFilterChoiceRepository
	) {
		$this->parameterFilterChoiceRepository = $parameterFilterChoiceRepository;
		$this->flagFilterChoiceRepository = $flagFilterChoiceRepository;
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
		$flagFilterChoices = $this->flagFilterChoiceRepository
			->getFlagFilterChoicesInCategory($domainId, $category);

		return new ProductFilterFormType($parameterFilterChoices, $flagFilterChoices);
	}

}
