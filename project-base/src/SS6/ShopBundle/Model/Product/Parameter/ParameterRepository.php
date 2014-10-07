<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Parameter\Parameter;

class ParameterRepository {

	/** 
	 * @var \Doctrine\ORM\EntityRepository
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
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
	 * @throws \SS6\ShopBundle\Model\Product\Parameter\Exception\ParameterNotFoundException
	 */
	public function getById($parameterId) {
		$parameter = $this->findById($parameterId);

		if ($parameter === null) {
			throw new \SS6\ShopBundle\Model\Product\Parameter\Exception\ParameterNotFoundException($parameterId);
		}

		return $parameter;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Parameter\Parameter[]
	 */
	public function findAll() {
		return $this->getParameterRepository()->findBy(array(), array('name' => 'asc'));
	}

	/**
	 * @param string $valueText
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ParameterValue
	 */
	public function findOrCreateParameterValueByValueText($valueText) {
		$parameterValue = $this->getParameterValueRepository()->findOneBy(array(
			'text' => $valueText,
		));

		if ($parameterValue === null) {
			$parameterValue = new ParameterValue(new ParameterValueData($valueText));
			$this->em->persist($parameterValue);
		}

		return $parameterValue;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue[]
	 */
	public function findParameterValuesByProduct(\SS6\ShopBundle\Model\Product\Product $product) {
		return $this->getProductParameterValueRepository()->findBy(array(
			'product' => $product,
		));
	}

	/**
	 * @param int $parameterId
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue[]
	 */
	public function findProductParameterValuesByParameterId($parameterId) {
		return $this->getProductParameterValueRepository()->findBy(array(
			'parameter' => $parameterId,
		));
		
	}
}
