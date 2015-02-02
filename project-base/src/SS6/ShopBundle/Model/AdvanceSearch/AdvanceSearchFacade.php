<?php

namespace SS6\ShopBundle\Model\AdvanceSearch;

use SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchConfig;
use SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchFormFactory;
use SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchService;
use SS6\ShopBundle\Model\Product\Listing\ProductListAdminFacade;
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

	/**
	 * @var \SS6\ShopBundle\Model\Product\Listing\ProductListAdminFacade
	 */
	private $productListAdminFacade;

	public function __construct(
		AdvanceSearchConfig $advanceSearchConfig,
		AdvanceSearchFormFactory $advanceSearchFormFactory,
		AdvanceSearchService $advanceSearchService,
		ProductListAdminFacade $productListAdminFacade
	) {
		$this->advanceSearchConfig = $advanceSearchConfig;
		$this->advanceSearchFormFactory = $advanceSearchFormFactory;
		$this->advanceSearchService = $advanceSearchService;
		$this->productListAdminFacade = $productListAdminFacade;
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
		$rulesData = [$this->advanceSearchService->createDefaultRuleFormData($filterName)];

		return $this->advanceSearchFormFactory->createRulesForm(self::RULES_FORM_NAME, $rulesData);
	}

	/**
	 * @param array $advanceSearchData
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getQueryBuilderByAdvanceSearchData($advanceSearchData) {
		$queryBuilder = $this->productListAdminFacade->getProductListQueryBuilder();
		$this->advanceSearchService->extendQueryBuilderByAdvanceSearchData($queryBuilder, $advanceSearchData);

		return $queryBuilder;
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return bool
	 */
	public function isAdvanceSearchFormSubmitted(Request $request) {
		$rulesData = $request->get(self::RULES_FORM_NAME);

		return $rulesData !== null;
	}

}
