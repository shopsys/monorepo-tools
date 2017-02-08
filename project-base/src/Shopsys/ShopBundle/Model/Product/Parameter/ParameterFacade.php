<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Parameter\ParameterData;
use SS6\ShopBundle\Model\Product\Parameter\ParameterRepository;
use SS6\ShopBundle\Model\Product\Parameter\ParameterService;

class ParameterFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterRepository
	 */
	private $parameterRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterService
	 */
	private $parameterService;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterRepository $parameterRepository
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterService $parameterService
	 */
	public function __construct(
		EntityManager $em,
		ParameterRepository $parameterRepository,
		ParameterService $parameterService
	) {
		$this->em = $em;
		$this->parameterRepository = $parameterRepository;
		$this->parameterService = $parameterService;
	}

	/**
	 * @param int $parameterId
	 * @return \SS6\ShopBundle\Model\Product\Parameter\Parameter
	 */
	public function getById($parameterId) {
		return $this->parameterRepository->getById($parameterId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterData $parameterData
	 * @return \SS6\ShopBundle\Model\Product\Parameter\Parameter
	 */
	public function create(ParameterData $parameterData) {
		$parameter = $this->parameterService->create($parameterData);
		$this->em->persist($parameter);
		$this->em->flush($parameter);

		return $parameter;
	}

	/**
	 * @param string[locale] $namesByLocale
	 * @return \SS6\ShopBundle\Model\Product\Parameter\Parameter|null
	 */
	public function findParameterByNames(array $namesByLocale) {
		return $this->parameterRepository->findParameterByNames($namesByLocale);
	}

	/**
	 * @param int $parameterId
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterData $parameterData
	 * @return \SS6\ShopBundle\Model\Product\Parameter\Parameter
	 */
	public function edit($parameterId, ParameterData $parameterData) {
		$parameter = $this->parameterRepository->getById($parameterId);
		$this->parameterService->edit($parameter, $parameterData);
		$this->em->flush();

		return $parameter;
	}

	/**
	 * @param int $parameterId
	 */
	public function deleteById($parameterId) {
		$parameter = $this->parameterRepository->getById($parameterId);

		$this->em->remove($parameter);
		$this->em->flush();
	}
}
