<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Parameter\Parameter;
use SS6\ShopBundle\Model\Product\Product;

class ParameterRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	public function __construct(
		EntityManager $entityManager
	) {
		$this->em = $entityManager;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getParameterRepository() {
		return $this->em->getRepository(Parameter::class);
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getParameterValueRepository() {
		return $this->em->getRepository(ParameterValue::class);
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getProductParameterValueRepository() {
		return $this->em->getRepository(ProductParameterValue::class);
	}

	/**
	 * @param int $parameterId
	 * @return \SS6\ShopBundle\Model\Product\Parameter\Parameter|null
	 */
	public function findById($parameterId) {
		return $this->getParameterRepository()->find($parameterId);
	}

	/**
	 * @param int $parameterId
	 * @return \SS6\ShopBundle\Model\Product\Parameter\Parameter
	 */
	public function getById($parameterId) {
		$parameter = $this->findById($parameterId);

		if ($parameter === null) {
			$message = 'Parameter with ID ' . $parameterId . ' not found.';
			throw new \SS6\ShopBundle\Model\Product\Parameter\Exception\ParameterNotFoundException($message);
		}

		return $parameter;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Parameter\Parameter[]
	 */
	public function findAll() {
		return $this->getParameterRepository()->findBy([], ['id' => 'asc']);
	}

	/**
	 * @param string $valueText
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ParameterValue
	 */
	public function findOrCreateParameterValueByValueText($valueText) {
		$parameterValue = $this->getParameterValueRepository()->findOneBy([
			'text' => $valueText,
		]);

		if ($parameterValue === null) {
			$parameterValue = new ParameterValue(new ParameterValueData($valueText));
			$this->em->persist($parameterValue);
			// Doctrine's identity map is not cache.
			// We have to flush now, so that next findOneBy() finds new ParameterValue.
			$this->em->flush();
		}

		return $parameterValue;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	private function getProductParameterValuesByProductQueryBuilder(Product $product) {
		$queryBuilder = $this->em->createQueryBuilder()
			->select('ppv')
			->from(ProductParameterValue::class, 'ppv')
			->where('ppv.product = :product_id')
			->setParameter('product_id', $product->getId());

		return $queryBuilder;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue[]
	 */
	public function getProductParameterValuesByProduct(Product $product) {
		$queryBuilder = $this->getProductParameterValuesByProductQueryBuilder($product);

		return $queryBuilder->getQuery()->execute();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue[]
	 */
	public function getProductParameterValuesByProductEagerLoaded(Product $product) {
		$queryBuilder = $this->getProductParameterValuesByProductQueryBuilder($product)
			->join('ppv.parameter', 'p')
			->join('p.translations', 'pt')
			->join('ppv.value', 'pv');

		return $queryBuilder->getQuery()->execute();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\Parameter $parameter
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue[]
	 */
	public function getProductParameterValuesByParameter(Parameter $parameter) {
		return $this->getProductParameterValueRepository()->findBy([
			'parameter' => $parameter,
		]);
	}
}
