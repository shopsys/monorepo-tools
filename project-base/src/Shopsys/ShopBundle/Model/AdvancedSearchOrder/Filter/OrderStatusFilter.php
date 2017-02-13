<?php

namespace Shopsys\ShopBundle\Model\AdvancedSearchOrder\Filter;

use Doctrine\ORM\QueryBuilder;
use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatusRepository;

class OrderStatusFilter implements AdvancedSearchFilterInterface
{
    private $orderStatusRepository;

    public function __construct(OrderStatusRepository $orderStatusRepository)
    {
        $this->orderStatusRepository = $orderStatusRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'orderStatus';
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedOperators()
    {
        return [
            self::OPERATOR_IS,
            self::OPERATOR_IS_NOT,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getValueFormType()
    {
        return FormType::CHOICE;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueFormOptions()
    {
        $orderStatusChoices = [];
        foreach ($this->orderStatusRepository->getAll() as $orderStatus) {
            /* @var $orderStatus \Shopsys\ShopBundle\Model\Order\Status\OrderStatus */
            $orderStatusChoices[$orderStatus->getId()] = $orderStatus->getName();
        }

        return [
            'choices' => $orderStatusChoices,
            'expanded' => false,
            'multiple' => false,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function extendQueryBuilder(QueryBuilder $queryBuilder, $rulesData)
    {
        foreach ($rulesData as $index => $ruleData) {
            $dqlOperator = $this->getContainsDqlOperator($ruleData->operator);
            $searchValue = $ruleData->value;
            $parameterName = 'orderStatusId_' . $index;
            $queryBuilder->andWhere('o.status ' . $dqlOperator . ' :' . $parameterName);
            $queryBuilder->setParameter($parameterName, $searchValue);
        }
    }

    /**
     * @param string $operator
     * @return string
     */
    private function getContainsDqlOperator($operator)
    {
        switch ($operator) {
            case self::OPERATOR_IS:
                return '=';
            case self::OPERATOR_IS_NOT:
                return '!=';
        }
    }
}
