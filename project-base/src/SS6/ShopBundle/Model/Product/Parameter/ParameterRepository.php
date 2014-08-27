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

}
