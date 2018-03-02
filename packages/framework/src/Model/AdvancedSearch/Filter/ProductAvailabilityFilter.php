<?php

namespace Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface;
use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProductAvailabilityFilter implements AdvancedSearchFilterInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade
     */
    private $availabilityFacade;

    public function __construct(AvailabilityFacade $availabilityFacade)
    {
        $this->availabilityFacade = $availabilityFacade;
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
    public function getName()
    {
        return 'productAvailability';
    }

    /**
     * {@inheritdoc}
     */
    public function getValueFormOptions()
    {
        return [
            'expanded' => false,
            'multiple' => false,
            'choices' => $this->availabilityFacade->getAll(),
            'choice_label' => 'name',
            'choice_value' => 'id',
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
    public function extendQueryBuilder(QueryBuilder $queryBuilder, $rulesData)
    {
        $isNotAvailabilities = [];

        foreach ($rulesData as $index => $ruleData) {
            if ($ruleData->operator === self::OPERATOR_IS) {
                $tableAlias = 'a' . $index;
                $availabilityParameter = 'availability' . $index;
                $queryBuilder->join('p.calculatedAvailability', $tableAlias, Join::WITH, $tableAlias . '.id = :' . $availabilityParameter);
                $queryBuilder->setParameter($availabilityParameter, $ruleData->value);
            } elseif ($ruleData->operator === self::OPERATOR_IS_NOT) {
                $isNotAvailabilities[] = $ruleData->value;
            }
        }

        if (count($isNotAvailabilities) > 0) {
            $subQuery = 'SELECT availability_p.id FROM ' . Product::class . ' availability_p
                JOIN availability_p.calculatedAvailability _a WITH _a.id IN (:isNotAvailabilities)';
            $queryBuilder->andWhere('p.id NOT IN (' . $subQuery . ')');
            $queryBuilder->setParameter('isNotAvailabilities', $isNotAvailabilities);
        }
    }
}
