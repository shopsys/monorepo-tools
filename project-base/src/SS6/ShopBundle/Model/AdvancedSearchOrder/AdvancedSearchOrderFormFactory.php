<?php

namespace SS6\ShopBundle\Model\AdvancedSearchOrder;

use SS6\ShopBundle\Form\Admin\AdvancedSearchOrder\AdvancedSearchOrderTranslation;
use SS6\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderConfig;
use SS6\ShopBundle\Model\AdvancedSearchOrder\RuleData;
use Symfony\Component\Form\FormFactoryInterface;

class AdvancedSearchOrderFormFactory {

	/**
	 * @var \Symfony\Component\Form\FormFactoryInterface
	 */
	private $formFactory;

	/**
	 * @var \SS6\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderConfig
	 */
	private $advancedSearchOrderConfig;

	/**
	 * @var \SS6\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderTranslation
	 */
	private $advancedSearchOrderTranslation;

	public function __construct(
		FormFactoryInterface $formFactory,
		AdvancedSearchOrderConfig $advancedSearchOrderConfig,
		AdvancedSearchOrderTranslation $advancedSearchOrderTranslation
	) {
		$this->formFactory = $formFactory;
		$this->advancedSearchOrderConfig = $advancedSearchOrderConfig;
		$this->advancedSearchOrderTranslation = $advancedSearchOrderTranslation;
	}

	/**
	 * @param string $name
	 * @param array $rulesViewData
	 * @return \Symfony\Component\Form\Form
	 */
	public function createRulesForm($name, $rulesViewData) {
		$formBuilder = $this->formFactory->createNamedBuilder($name, 'form', null, ['csrf_protection' => false]);

		foreach ($rulesViewData as $ruleKey => $ruleViewData) {
			$ruleFilter = $this->advancedSearchOrderConfig->getFilter($ruleViewData['subject']);
			$formBuilder->add($this->createRuleFormBuilder($ruleKey, $ruleFilter));
		}

		$form = $formBuilder->getForm();
		$form->submit($rulesViewData);

		return $form;
	}

	/**
	 * @param string $name
	 * @param \SS6\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderFilterInterface $ruleFilter
	 * @return \Symfony\Component\Form\Form
	 */
	private function createRuleFormBuilder($name, AdvancedSearchOrderFilterInterface $ruleFilter) {
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
	 * @param \SS6\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderFilterInterface $filter
	 * @return string[]
	 */
	private function getFilterOperatorChoices(AdvancedSearchOrderFilterInterface $filter) {
		$choices = [];
		foreach ($filter->getAllowedOperators() as $operator) {
			$choices[$operator] = $this->advancedSearchOrderTranslation->translateOperator($operator);
		}

		return $choices;
	}

	/**
	 * @return string[]
	 */
	private function getSubjectChoices() {
		$choices = [];
		foreach ($this->advancedSearchOrderConfig->getAllFilters() as $filter) {
			$choices[$filter->getName()] = $this->advancedSearchOrderTranslation->translateFilterName($filter->getName());
		}

		return $choices;
	}
}
