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
	 * @param string $entityClass
	 * @param array $rowIds
	 */
	public function saveOrdering($entityClass, array $rowIds) {
		$entityRepository = $this->em->getRepository($entityClass);
		$position = 0;

		foreach ($rowIds as $rowId) {
			$entity = $entityRepository->find($rowId);
			$this->gridOrderingService->setPosition($entity, $position++);
		}

		$this->em->flush();
	}

}
