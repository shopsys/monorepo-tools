<?php

namespace Shopsys\ShopBundle\Model\AdvancedSearch;

use Shopsys\ShopBundle\Model\AdvancedSearch\AdvancedSearchService;
use Shopsys\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchFormFactory;
use Shopsys\ShopBundle\Model\Product\Listing\ProductListAdminFacade;
use Symfony\Component\HttpFoundation\Request;

class AdvancedSearchFacade {

    const RULES_FORM_NAME = 'as';

    /**
     * @var \Shopsys\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchFormFactory
     */
    private $advancedSearchFormFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\AdvancedSearch\AdvancedSearchService
     */
    private $advancedSearchService;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Listing\ProductListAdminFacade
     */
    private $productListAdminFacade;

    public function __construct(
        ProductAdvancedSearchFormFactory $advancedSearchFormFactory,
        AdvancedSearchService $advancedSearchService,
        ProductListAdminFacade $productListAdminFacade
    ) {
        $this->advancedSearchFormFactory = $advancedSearchFormFactory;
        $this->advancedSearchService = $advancedSearchService;
        $this->productListAdminFacade = $productListAdminFacade;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\Form\Form
     */
    public function createAdvancedSearchForm(Request $request) {
        $rulesData = (array)$request->get(self::RULES_FORM_NAME, null, true);
        $rulesFormData = $this->advancedSearchService->getRulesFormViewDataByRequestData($rulesData);

        return $this->advancedSearchFormFactory->createRulesForm(self::RULES_FORM_NAME, $rulesFormData);
    }

    /**
     * @param string $filterName
     * @return \Symfony\Component\Form\Form
     */
    public function createRuleForm($filterName, $index) {
        $rulesData = [
            $index => $this->advancedSearchService->createDefaultRuleFormViewData($filterName),
        ];

        return $this->advancedSearchFormFactory->createRulesForm(self::RULES_FORM_NAME, $rulesData);
    }

    /**
     * @param array $advancedSearchData
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderByAdvancedSearchData($advancedSearchData) {
        $queryBuilder = $this->productListAdminFacade->getProductListQueryBuilder();
        $this->advancedSearchService->extendQueryBuilderByAdvancedSearchData($queryBuilder, $advancedSearchData);

        return $queryBuilder;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return bool
     */
    public function isAdvancedSearchFormSubmitted(Request $request) {
        $rulesData = $request->get(self::RULES_FORM_NAME);

        return $rulesData !== null;
    }

}
