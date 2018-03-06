<?php

namespace Shopsys\FrameworkBundle\Model\AdvancedSearchOrder\Filter;

use Doctrine\ORM\QueryBuilder;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusFacade;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class OrderStatusFilter implements AdvancedSearchFilterInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusFacade
     */
    private $orderStatusFacade;

    public function __construct(OrderStatusFacade $orderStatusFacade)
    {
        $this->orderStatusFacade = $orderStatusFacade;
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
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueFormOptions()
    {
        return [
            'choices' => $this->orderStatusFacade->getAll(),
            'choice_label' => 'name',
            'choice_value' => 'id',
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
