<?php

namespace Shopsys\FrameworkBundle\Model\AdvancedSearch;

use Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductNameFilter;
use Shopsys\FrameworkBundle\Model\Product\Listing\ProductListAdminFacade;
use Symfony\Component\HttpFoundation\Request;

class AdvancedSearchProductFacade
{
    protected const RULES_FORM_NAME = 'as';

    /**
     * @var \Shopsys\FrameworkBundle\Model\AdvancedSearch\ProductAdvancedSearchFormFactory
     */
    protected $advancedSearchFormFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchQueryBuilderExtender
     */
    protected $advancedSearchQueryBuilderExtender;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Listing\ProductListAdminFacade
     */
    protected $productListAdminFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\AdvancedSearch\RuleFormViewDataFactory
     */
    protected $ruleFormViewDataFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\ProductAdvancedSearchFormFactory $advancedSearchFormFactory
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchQueryBuilderExtender $advancedSearchQueryBuilderExtender
     * @param \Shopsys\FrameworkBundle\Model\Product\Listing\ProductListAdminFacade $productListAdminFacade
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\RuleFormViewDataFactory $ruleFormViewDataFactory
     */
    public function __construct(
        ProductAdvancedSearchFormFactory $advancedSearchFormFactory,
        AdvancedSearchQueryBuilderExtender $advancedSearchQueryBuilderExtender,
        ProductListAdminFacade $productListAdminFacade,
        RuleFormViewDataFactory $ruleFormViewDataFactory
    ) {
        $this->advancedSearchFormFactory = $advancedSearchFormFactory;
        $this->advancedSearchQueryBuilderExtender = $advancedSearchQueryBuilderExtender;
        $this->productListAdminFacade = $productListAdminFacade;
        $this->ruleFormViewDataFactory = $ruleFormViewDataFactory;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createAdvancedSearchForm(Request $request)
    {
        $rawRulesData = $request->get(static::RULES_FORM_NAME);
        $rulesData = is_array($rawRulesData) ? $rawRulesData : [];
        $rulesFormData = $this->ruleFormViewDataFactory->createFromRequestData(ProductNameFilter::NAME, $rulesData);

        return $this->advancedSearchFormFactory->createRulesForm(static::RULES_FORM_NAME, $rulesFormData);
    }

    /**
     * @param string $filterName
     * @param string|int $index
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createRuleForm($filterName, $index)
    {
        $rulesData = [
            $index => $this->ruleFormViewDataFactory->createDefault($filterName),
        ];

        return $this->advancedSearchFormFactory->createRulesForm(static::RULES_FORM_NAME, $rulesData);
    }

    /**
     * @param array $advancedSearchData
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderByAdvancedSearchData($advancedSearchData)
    {
        $queryBuilder = $this->productListAdminFacade->getProductListQueryBuilder();
        $this->advancedSearchQueryBuilderExtender->extendByAdvancedSearchData($queryBuilder, $advancedSearchData);

        return $queryBuilder;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return bool
     */
    public function isAdvancedSearchFormSubmitted(Request $request)
    {
        $rulesData = $request->get(static::RULES_FORM_NAME);

        return $rulesData !== null;
    }
}
