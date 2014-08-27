<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Parameter\ParameterData;
use SS6\ShopBundle\Model\Product\Parameter\ParameterService;
use SS6\ShopBundle\Model\Product\Parameter\ParameterRepository;

class ParameterFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterRepository
	 */
	private $availabiityRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterService
	 */
	private $parameterService;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterRepository $availabiityRepository
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterService $parameterService
	 */
	public function __construct(
		EntityManager $em,
		ParameterRepository $availabiityRepository,
		ParameterService $parameterService
	) {
		$this->em = $em;
		$this->availabiityRepository = $availabiityRepository;
		$this->parameterService = $parameterService;
	}

	/**
	 * @param int $parameterId
	 * @return \SS6\ShopBundle\Model\Product\Parameter\
	 */
	public function getById($parameterId) {
		return $this->availabiityRepository->getById($parameterId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterData $parameterData
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ParameterData
	 */
	public function create(ParameterData $parameterData) {
		$parameter = $this->parameterService->create($parameterData);
		$this->em->persist($parameter);
		$this->em->flush();

		return $parameter;
	}

	/**
	 * @param int $parameterId
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterData $parameterData
	 * @return \SS6\ShopBundle\Model\Product\Parameter\Parameter
	 */
	public function edit($parameterId, ParameterData $parameterData) {
		$parameter = $this->availabiityRepository->getById($parameterId);
		$this->parameterService->edit($parameter, $parameterData);
		$this->em->flush();

		return $parameter;
	}

	/**
	 * @param int $parameterId
	 */
	public function deleteById($parameterId) {
		$parameter = $this->availabiityRepository->getById($parameterId);
		
		$this->em->remove($parameter);
		$this->em->flush();
	}

}
