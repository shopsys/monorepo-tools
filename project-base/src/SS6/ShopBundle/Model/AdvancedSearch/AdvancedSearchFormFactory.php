<?php

namespace SS6\ShopBundle\Model\AdvancedSearch;

use SS6\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchTranslation;
use SS6\ShopBundle\Model\AdvancedSearch\AdvancedSearchConfig;
use SS6\ShopBundle\Model\AdvancedSearch\RuleData;
use Symfony\Component\Form\FormFactoryInterface;

class AdvancedSearchFormFactory {

	/**
	 * @var \Symfony\Component\Form\FormFactoryInterface
	 */
	private $formFactory;

	/**
	 * @var \SS6\ShopBundle\Model\AdvancedSearch\AdvancedSearchConfig
	 */
	private $advancedSearchConfig;

	/**
	 * @var \SS6\ShopBundle\Model\AdvancedSearch\AdvancedSearchTranslation
	 */
	private $advancedSearchTranslation;

	public function __construct(
		FormFactoryInterface $formFactory,
		AdvancedSearchConfig $advancedSearchConfig,
		AdvancedSearchTranslation $advancedSearchTranslation
	) {
		$this->formFactory = $formFactory;
		$this->advancedSearchConfig = $advancedSearchConfig;
		$this->advancedSearchTranslation = $advancedSearchTranslation;
	}

	/**
	 * @param string $name
	 * @param array $rulesViewData
	 * @return \Symfony\Component\Form\Form
	 */
	public function createRulesForm($name, $rulesViewData) {
		$formBuilder = $this->formFactory->createNamedBuilder($name, 'form', null, ['csrf_protection' => false]);

		foreach ($rulesViewData as $ruleKey => $ruleViewData) {
			$ruleFilter = $this->advancedSearchConfig->getFilter($ruleViewData['subject']);
			$formBuilder->add($this->createRuleFormBuilder($ruleKey, $ruleFilter));
		}

		$form = $formBuilder->getForm();
		$form->submit($rulesViewData);

		return $form;
	}

	/**
	 * @param string $name
	 * @param \SS6\ShopBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface $ruleFilter
	 * @return \Symfony\Component\Form\Form
	 */
	private function createRuleFormBuilder($name, AdvancedSearchFilterInterface $ruleFilter) {
		$filterFormBuilder = $this->formFactory->createNamedBuilder($name, 'form', null, [
			'data_class' => RuleData::class,
		])
			->add('subject', 'choice', [
					'choices' => $this->getSubjectChoices(),
					'expanded' => false,
					'multiple' => false,
				])
			->add('operator', 'choice', [
					'choices' => $this->getFilterOperatorChoices($ruleFilter),
					'expanded' => false,
					'multiple' => false,
				])
			->add('value', $ruleFilter->getValueFormType(), $ruleFilter->getValueFormOptions());

		return $filterFormBuilder;
	}

	/**
	 * @param \SS6\ShopBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface $filter
	 * @return string[]
	 */
	private function getFilterOperatorChoices(AdvancedSearchFilterInterface $filter) {
		$choices = [];
		foreach ($filter->getAllowedOperators() as $operator) {
			$choices[$operator] = $this->advancedSearchTranslation->translateOperator($operator);
		}

		return $choices;
	}

	/**
	 * @return string[]
	 */
	private function getSubjectChoices() {
		$choices = [];
		foreach ($this->advancedSearchConfig->getAllFilters() as $filter) {
			$choices[$filter->getName()] = $this->advancedSearchTranslation->translateFilterName($filter->getName());
		}

		return $choices;
	}
}
