<?php

namespace Shopsys\FrameworkBundle\Model\AdvancedSearchOrder;

use Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchQueryBuilderExtender;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\OrderAdvancedSearchFormFactory;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\RuleFormViewDataFactory;
use Shopsys\FrameworkBundle\Model\Order\Listing\OrderListAdminFacade;
use Symfony\Component\HttpFoundation\Request;

class AdvancedSearchOrderFacade
{
    const RULES_FORM_NAME = 'as';

    /**
     * @var \Shopsys\FrameworkBundle\Model\AdvancedSearch\OrderAdvancedSearchFormFactory
     */
    protected $orderAdvancedSearchFormFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchQueryBuilderExtender
     */
    protected $advancedSearchQueryBuilderExtender;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Listing\OrderListAdminFacade
     */
    protected $orderListAdminFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\AdvancedSearch\RuleFormViewDataFactory
     */
    protected $ruleFormViewDataFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\OrderAdvancedSearchFormFactory $orderAdvancedSearchFormFactory
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchQueryBuilderExtender $advancedSearchQueryBuilderExtender
     * @param \Shopsys\FrameworkBundle\Model\Order\Listing\OrderListAdminFacade $orderListAdminFacade
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\RuleFormViewDataFactory $ruleFormViewDataFactory
     */
    public function __construct(
        OrderAdvancedSearchFormFactory $orderAdvancedSearchFormFactory,
        AdvancedSearchQueryBuilderExtender $advancedSearchQueryBuilderExtender,
        OrderListAdminFacade $orderListAdminFacade,
        RuleFormViewDataFactory $ruleFormViewDataFactory
    ) {
        $this->orderAdvancedSearchFormFactory = $orderAdvancedSearchFormFactory;
        $this->advancedSearchQueryBuilderExtender = $advancedSearchQueryBuilderExtender;
        $this->orderListAdminFacade = $orderListAdminFacade;
        $this->ruleFormViewDataFactory = $ruleFormViewDataFactory;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createAdvancedSearchOrderForm(Request $request)
    {
        $rulesData = (array)$request->get(self::RULES_FORM_NAME);
        $rulesFormData = $this->ruleFormViewDataFactory->createFromRequestData('orderTotalPriceWithVat', $rulesData);

        return $this->orderAdvancedSearchFormFactory->createRulesForm(self::RULES_FORM_NAME, $rulesFormData);
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

        return $this->orderAdvancedSearchFormFactory->createRulesForm(self::RULES_FORM_NAME, $rulesData);
    }

    /**
     * @param array $advancedSearchOrderData
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderByAdvancedSearchOrderData($advancedSearchOrderData)
    {
        $queryBuilder = $this->orderListAdminFacade->getOrderListQueryBuilder();
        $this->advancedSearchQueryBuilderExtender->extendByAdvancedSearchData($queryBuilder, $advancedSearchOrderData);

        return $queryBuilder;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return bool
     */
    public function isAdvancedSearchOrderFormSubmitted(Request $request)
    {
        $rulesData = $request->get(self::RULES_FORM_NAME);

        return $rulesData !== null;
    }
}
