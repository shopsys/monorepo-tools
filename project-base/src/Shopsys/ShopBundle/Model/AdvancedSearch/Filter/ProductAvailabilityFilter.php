<?php

namespace SS6\ShopBundle\Model\AdvancedSearch\Filter;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade;
use SS6\ShopBundle\Model\Product\Product;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;

class ProductAvailabilityFilter implements AdvancedSearchFilterInterface{

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade
	 */
	private $availabilityFacade;

	public function __construct(AvailabilityFacade $availabilityFacade) {
		$this->availabilityFacade = $availabilityFacade;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAllowedOperators() {
		return [
			self::OPERATOR_IS,
			self::OPERATOR_IS_NOT,
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'productAvailability';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getValueFormOptions() {
		return [
			'expanded' => false,
			'multiple' => false,
			'choice_list' => new ObjectChoiceList($this->availabilityFacade->getAll(), 'name', [], null, 'id'),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getValueFormType() {
		return FormType::CHOICE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function extendQueryBuilder(QueryBuilder $queryBuilder, $rulesData) {
		$isNotAvailabilities = [];

		foreach ($rulesData as $index => $ruleData) {
			/* @var $ruleData \SS6\ShopBundle\Model\AdvancedSearch\AdvancedSearchRuleData */
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
