<?php

namespace Shopsys\ShopBundle\Model\AdvancedSearchOrder;

use Shopsys\ShopBundle\Model\AdvancedSearch\OrderAdvancedSearchFormFactory;
use Shopsys\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderService;
use Shopsys\ShopBundle\Model\Order\Listing\OrderListAdminFacade;
use Symfony\Component\HttpFoundation\Request;

class AdvancedSearchOrderFacade
{
    const RULES_FORM_NAME = 'as';

    /**
     * @var \Shopsys\ShopBundle\Model\AdvancedSearch\OrderAdvancedSearchFormFactory
     */
    private $orderAdvancedSearchFormFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderService
     */
    private $advancedSearchOrderService;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Listing\OrderListAdminFacade
     */
    private $orderListAdminFacade;

    public function __construct(
        OrderAdvancedSearchFormFactory $orderAdvancedSearchFormFactory,
        AdvancedSearchOrderService $advancedSearchOrderService,
        OrderListAdminFacade $orderListAdminFacade
    ) {
        $this->orderAdvancedSearchFormFactory = $orderAdvancedSearchFormFactory;
        $this->advancedSearchOrderService = $advancedSearchOrderService;
        $this->orderListAdminFacade = $orderListAdminFacade;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\Form\Form
     */
    public function createAdvancedSearchOrderForm(Request $request)
    {
        $rulesData = (array)$request->get(self::RULES_FORM_NAME);
        $rulesFormData = $this->advancedSearchOrderService->getRulesFormViewDataByRequestData($rulesData);

        return $this->orderAdvancedSearchFormFactory->createRulesForm(self::RULES_FORM_NAME, $rulesFormData);
    }

    /**
     * @param string $filterName
     * @return \Symfony\Component\Form\Form
     */
    public function createRuleForm($filterName, $index)
    {
        $rulesData = [
            $index => $this->advancedSearchOrderService->createDefaultRuleFormViewData($filterName),
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
        $this->advancedSearchOrderService->extendQueryBuilderByAdvancedSearchOrderData($queryBuilder, $advancedSearchOrderData);

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
