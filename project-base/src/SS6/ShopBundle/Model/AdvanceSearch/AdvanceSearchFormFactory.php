<?php

namespace SS6\ShopBundle\Model\AdvanceSearch;

use Symfony\Component\Form\FormFactoryInterface;
use SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchConfig;

class AdvanceSearchFormFactory {
	
	/**
	 * @var \Symfony\Component\Form\FormFactoryInterface
	 */
	private $formFactory;

	/**
	 * @var \SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchConfig
	 */
	private $advanceSearchConfig;

	public function __construct(FormFactoryInterface $formFactory, AdvanceSearchConfig $advanceSearchConfig) {
		$this->formFactory = $formFactory;
		$this->advanceSearchConfig = $advanceSearchConfig;
	}

	/**
	 * @param string $name
	 * @param array $rulesData
	 * @return \Symfony\Component\Form\Form
	 */
	public function createRulesForm($name, $rulesData) {
		$formBuilder = $this->formFactory->createNamedBuilder($name, 'form', null, array('csrf_protection' => false));

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
			->add('subject','choice', [
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
			$choices[$operator] = $operator;
		}

		return $choices;
	}

	/**
	 * @return string[]
	 */
	private function getSubjectChoices() {
		$choices = [];
		foreach ($this->advanceSearchConfig->getAllFilters() as $filter) {
			$choices[$filter->getName()] = $filter->getName();
		}

		return $choices;
	}
}
