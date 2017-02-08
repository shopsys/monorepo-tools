<?php

namespace SS6\ShopBundle\Model\Product\Flag;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Flag\FlagData;
use SS6\ShopBundle\Model\Product\Flag\FlagRepository;
use SS6\ShopBundle\Model\Product\Flag\FlagService;

class FlagFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Flag\FlagRepository
	 */
	private $flagRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Flag\FlagService
	 */
	private $flagService;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Product\Flag\FlagRepository $flagRepository
	 * @param \SS6\ShopBundle\Model\Product\Flag\FlagService $flagService
	 */
	public function __construct(
		EntityManager $em,
		FlagRepository $flagRepository,
		FlagService $flagService
	) {
		$this->em = $em;
		$this->flagRepository = $flagRepository;
		$this->flagService = $flagService;
	}

	/**
	 * @param int $flagId
	 * @return \SS6\ShopBundle\Model\Product\Flag\Flag
	 */
	public function getById($flagId) {
		return $this->flagRepository->getById($flagId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Flag\FlagData $flagData
	 * @return \SS6\ShopBundle\Model\Product\Flag\Flag
	 */
	public function create(FlagData $flagData) {
		$flag = $this->flagService->create($flagData);
		$this->em->persist($flag);
		$this->em->flush();

		return $flag;
	}

	/**
	 * @param int $flagId
	 * @param \SS6\ShopBundle\Model\Product\Flag\FlagData $flagData
	 * @return \SS6\ShopBundle\Model\Product\Flag\Flag
	 */
	public function edit($flagId, FlagData $flagData) {
		$flag = $this->flagRepository->getById($flagId);
		$this->flagService->edit($flag, $flagData);
		$this->em->flush();

		return $flag;
	}

	/**
	 * @param int $flagId
	 */
	public function deleteById($flagId) {
		$flag = $this->flagRepository->getById($flagId);

		$this->em->remove($flag);
		$this->em->flush();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Flag\Flag[]
	 */
	public function getAll() {
		return $this->flagRepository->getAll();
	}

}
