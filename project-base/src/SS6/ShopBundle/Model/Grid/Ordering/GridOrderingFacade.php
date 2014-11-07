<?php

namespace SS6\ShopBundle\Model\Grid\Ordering;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Grid\Ordering\GridOrderingService;

class GridOrderingFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Grid\Ordering\GridOrderingService
	 */
	private $gridOrderingService;

	public function __construct(EntityManager $em, GridOrderingService $gridOrderingService) {
		$this->em = $em;
		$this->gridOrderingService = $gridOrderingService;
	}

	/**
	 * @param string $entityName
	 * @param array $rowIds
	 */
	public function saveOrdering($entityName, array $rowIds) {
		$entityRepository = $this->em->getRepository($this->gridOrderingService->getEntityClass($entityName));
		$position = 0;

		foreach ($rowIds as $rowId) {
			$entity = $entityRepository->find($rowId);
			$this->gridOrderingService->setPosition($entity, $position++);
		}

		$this->em->flush();
	}

}
