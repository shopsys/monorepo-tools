<?php

namespace SS6\ShopBundle\Model\Administrator;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Administrator\Administrator;
use SS6\ShopBundle\Model\Administrator\AdministratorGridService;
use SS6\ShopBundle\Model\PKGrid\PKGrid;

class AdministratorGridFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager;
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Administrator\AdministratorGridService
	 */
	private $administratorGridService;

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\AdministratorGridService $administratorGridService
	 */
	public function __construct(EntityManager $em, AdministratorGridService $administratorGridService) {
		$this->em = $em;
		$this->administratorGridService = $administratorGridService;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\Administrator $administrator
	 * @param \SS6\ShopBundle\Model\PKGrid\PKGrid $grid
	 */
	public function restoreAndRememberGridLimit(Administrator $administrator, PKGrid $grid) {
		$this->administratorGridService->restoreGridLimit($administrator, $grid);
		$gridLimit = $this->administratorGridService->rememberGridLimit($administrator, $grid);
		$this->em->persist($gridLimit);
		$this->em->flush();
	}
	
}
