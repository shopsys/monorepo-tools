<?php

namespace SS6\ShopBundle\Model\AdvanceSearch;

use SS6\ShopBundle\Form\Admin\AdvanceSearch\AdvanceSearchTranslation;
use SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchConfig;
use Symfony\Component\Form\FormFactoryInterface;

class AdvanceSearchFormFactory {

	/**
	 * @var \Symfony\Component\Form\FormFactoryInterface
	 */
	private $formFactory;

	/**
	 * @var \SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchConfig
	 */
	private $advanceSearchConfig;

	/**
	 * @var \SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchTranslation
	 */
	private $advanceSearchTranslation;

	public function __construct(
		FormFactoryInterface $formFactory,
		AdvanceSearchConfig $advanceSearchConfig,
		AdvanceSearchTranslation $advanceSearchTranslation
	) {
		$this->formFactory = $formFactory;
		$this->advanceSearchConfig = $advanceSearchConfig;
		$this->advanceSearchTranslation = $advanceSearchTranslation;
	}

	/**
	 * @param string $name
	 * @param array $rulesData
	 * @return \Symfony\Component\Form\Form
	 */
	public function createRulesForm($name, $rulesData) {
		$formBuilder = $this->formFactory->createNamedBuilder($name, 'form', null, ['csrf_protection' => false]);

		foreach ($rulesData as $ruleKey => $ruleData) {
			$ruleFilter = $this->advanceSearchConfig->getFilter($ruleData['subject']);
			$formBuilder->add($this->createRuleFormBuilder($ruleKey, $ruleFilter));
		}

		$form = $formBuilder->getForm();
		$form->submit($rulesData);

		return $form;
	}

	/**
	 * @param string $name
	 * @param \SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchFilterInterface $ruleFilter
	 * @return \Symfony\Component\Form\Form
	 */
	private function createRuleFormBuilder($name, AdvanceSearchFilterInterface $ruleFilter) {
		$filterFormBuilder = $this->formFactory->createNamedBuilder($name)
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
	 * @param \SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchFilterInterface $filter
	 * @return string[]
	 */
	private function getFilterOperatorChoices(AdvanceSearchFilterInterface $filter) {
		$choices = [];
		foreach ($filter->getAllowedOperators() as $operator) {
			$choices[$operator] = $this->advanceSearchTranslation->translateOperator($operator);
		}

		return $choices;
	}

	/**
	 * @return string[]
	 */
	private function getSubjectChoices() {
		$choices = [];
		foreach ($this->advanceSearchConfig->getAllFilters() as $filter) {
			$choices[$filter->getName()] = $this->advanceSearchTranslation->translateFilterName($filter->getName());
		}

		return $choices;
	}
}
