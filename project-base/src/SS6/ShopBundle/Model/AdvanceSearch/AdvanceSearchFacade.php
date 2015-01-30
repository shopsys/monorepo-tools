<?php

namespace SS6\ShopBundle\Model\AdvanceSearch;

use SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchConfig;
use SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchFormFactory;
use SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchService;
use Symfony\Component\HttpFoundation\Request;

class AdvanceSearchFacade {

	const RULES_FORM_NAME = 'af';

	/**
	 * @var \SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchConfig
	 */
	private $advanceSearchConfig;

	/**
	 * @var \SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchFormFactory
	 */
	private $advanceSearchFormFactory;

	/**
	 * @var \SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchService
	 */
	private $advanceSearchService;

	public function __construct(
		AdvanceSearchConfig $advanceSearchConfig,
		AdvanceSearchFormFactory $advanceSearchFormFactory,
		AdvanceSearchService $advanceSearchService
	) {
		$this->advanceSearchConfig = $advanceSearchConfig;
		$this->advanceSearchFormFactory = $advanceSearchFormFactory;
		$this->advanceSearchService = $advanceSearchService;
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Component\Form\Form
	 */
	public function createAdvanceSearchForm(Request $request) {
		$rulesData = (array)$request->get(self::RULES_FORM_NAME, null, true);
		$rulesFormData = $this->advanceSearchService->getRulesFormDataByRequestData($rulesData);
		
		return $this->advanceSearchFormFactory->createRulesForm(self::RULES_FORM_NAME, $rulesFormData);
	}

	/**
	 * @param string $filterName
	 * @return \Symfony\Component\Form\Form
	 */
	public function createRuleForm($filterName) {
		$rulesData = $this->advanceSearchService->createDefaultRuleFormData($filterName);

		return $this->advanceSearchFormFactory->createRulesForm(self::RULES_FORM_NAME, $rulesData);
	}

}
