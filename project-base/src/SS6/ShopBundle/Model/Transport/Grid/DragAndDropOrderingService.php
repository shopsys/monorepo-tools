<?php

namespace SS6\ShopBundle\Model\Transport\Grid;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Grid\DragAndDrop\GridOrderingInterface;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportRepository;

class DragAndDropOrderingService implements GridOrderingInterface {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportRepository
	 */
	private $transportRepository;

	public function __construct(EntityManager $em, TransportRepository $transportRepository) {
		$this->em = $em;
		$this->transportRepository = $transportRepository;
	}

	/**
	 * @return string
	 */
	public function getServiceName() {
		return 'ss6.shop.transport.grid.drag_and_drop_ordering_service';
	}

	/**
	 * @param array $rowIds
	 */
	public function saveOrder(array $rowIds) {
		$position = 0;

		foreach ($rowIds as $rowId) {
			$transport = $this->transportRepository->findById($rowId);

			if ($transport instanceof Transport) {
				$transport->setPosition($position);
			}

			$position++;
		}

		$this->em->flush();
	}

}
