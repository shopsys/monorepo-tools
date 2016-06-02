<?php

namespace SS6\ShopBundle\Model\Product\Filter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Component\Doctrine\GroupedScalarHydrator;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Product\Filter\ParameterFilterChoice;
use SS6\ShopBundle\Model\Product\Parameter\Parameter;
use SS6\ShopBundle\Model\Product\Parameter\ParameterValue;
use SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue;
use SS6\ShopBundle\Model\Product\ProductRepository;

class ParameterFilterChoiceRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	public function __construct(
		EntityManager $em,
		ProductRepository $productRepository
	) {
		$this->em = $em;
		$this->productRepository = $productRepository;
	}

	/**
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param string $locale
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @return \SS6\ShopBundle\Model\Product\Filter\ParameterFilterChoice[]
	 */
	public function getParameterFilterChoicesInCategory(
		$domainId,
		PricingGroup $pricingGroup,
		$locale,
		Category $category
	) {
		$productsQueryBuilder = $this->productRepository->getListableInCategoryQueryBuilder(
			$domainId,
			$pricingGroup,
			$category
		);

		$productsQueryBuilder
			->select('MIN(p), pp, pv')
			->join(ProductParameterValue::class, 'ppv', Join::WITH, 'ppv.product = p')
			->join(Parameter::class, 'pp', Join::WITH, 'pp = ppv.parameter')
			->join(ParameterValue::class, 'pv', Join::WITH, 'pv = ppv.value AND pv.locale = :locale')
			->groupBy('pp, pv')
			->resetDQLPart('orderBy')
			->setParameter('locale', $locale);

		$rows = $productsQueryBuilder->getQuery()->execute(null, GroupedScalarHydrator::HYDRATION_MODE);

		$visibleParametersIndexedById = $this->getVisibleParametersIndexedById($rows);
		$parameterValuesIndexedByParameterId = $this->getParameterValuesIndexedByParameterId($rows);

		$parameterFilterChoices = [];

		foreach ($parameterValuesIndexedByParameterId as $parameterId => $values) {
			if (array_key_exists($parameterId, $visibleParametersIndexedById)) {
				$parameterFilterChoices[] = new ParameterFilterChoice(
					$visibleParametersIndexedById[$parameterId],
					$values
				);
			}
		}

		return $parameterFilterChoices;
	}

	/**
	 * @param array $rows
	 * @return \SS6\ShopBundle\Model\Product\Parameter\Parameter[]
	 */
	private function getVisibleParametersIndexedById(array $rows) {
		$parameterIds = [];
		foreach ($rows as $row) {
			$parameterIds[$row['pp']['id']] = $row['pp']['id'];
		}

		$parametersQueryBuilder = $this->em->createQueryBuilder()
			->select('pp, pt')
			->from(Parameter::class, 'pp')
			->join('pp.translations', 'pt')
			->where('pp.id IN (:parameterIds)')
			->andWhere('pp.visible = true');
		$parametersQueryBuilder->setParameter('parameterIds', $parameterIds);
		$parameters = $parametersQueryBuilder->getQuery()->execute();

		$parametersIndexedById = [];
		foreach ($parameters as $parameter) {
			/* @var $parameter \SS6\ShopBundle\Model\Product\Parameter\Parameter */
			$parametersIndexedById[$parameter->getId()] = $parameter;
		}

		return $parametersIndexedById;
	}

	/**
	 * @param array $rows
	 * @return \SS6\ShopBundle\Model\Product\Parameter\Parameter[][]
	 */
	private function getParameterValuesIndexedByParameterId(array $rows) {
		$valuesIndexedById = $this->getParameterValuesIndexedById($rows);

		$valuesIndexedByParameterId = [];
		foreach ($rows as $row) {
			$valuesIndexedByParameterId[$row['pp']['id']][] = $valuesIndexedById[$row['pv']['id']];
		}

		return $valuesIndexedByParameterId;
	}

	/**
	 * @param array $rows
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ParameterValue[]
	 */
	private function getParameterValuesIndexedById(array $rows) {
		$valueIds = [];
		foreach ($rows as $row) {
			$valueIds[$row['pv']['id']] = $row['pv']['id'];
		}

		$valuesQueryBuilder = $this->em->createQueryBuilder()
			->select('pv')
			->from(ParameterValue::class, 'pv')
			->where('pv.id IN (:valueIds)');
		$valuesQueryBuilder->setParameter('valueIds', $valueIds);
		$values = $valuesQueryBuilder->getQuery()->execute();

		$valuesIndexedById = [];
		foreach ($values as $value) {
			/* @var $value \SS6\ShopBundle\Model\Product\Parameter\ParameterValue */
			$valuesIndexedById[$value->getId()] = $value;
		}

		return $valuesIndexedById;
	}

}
