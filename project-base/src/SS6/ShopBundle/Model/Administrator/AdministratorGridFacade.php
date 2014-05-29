<?php

namespace SS6\ShopBundle\Model\Administrator;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Administrator\Administrator;
use SS6\ShopBundle\Model\Administrator\AdministratorGridService;

class AdministratorGridFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager;
	 */
	private $em;

	/**
	 * @var SS6\ShopBundle\Model\Administrator\AdministratorGridService
	 */
	private $administratorGridService;

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\AdministratorGridService $administratorGridService
	 */
	public function __construct(EntityManager $em, AdministratorGridService $administratorGridService) {
		$this->em = $em;
		$this->administratorGridService = $administratorGridService;
	}

	public function saveGridLimit(Administrator $administrator, $gridId, $limit) {
		$gridLimit = $this->administratorGridService->setGridLimit($administrator, $gridId, $limit);
		$this->em->persist($gridLimit);
		$this->em->flush();
	}
	
}
