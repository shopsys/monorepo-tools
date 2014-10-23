<?php

namespace SS6\ShopBundle\Model\Payment\Grid;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Grid\DragAndDrop\GridOrderingInterface;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentRepository;

class DragAndDropOrderingService implements GridOrderingInterface {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PaymentRepository
	 */
	private $paymentRepository;

	public function __construct(EntityManager $em, PaymentRepository $paymentRepository) {
		$this->em = $em;
		$this->paymentRepository = $paymentRepository;
	}

	/**
	 * @return string
	 */
	public function getServiceName() {
		return 'ss6.shop.payment.grid.drag_and_drop_ordering_service';
	}

	/**
	 * @param array $rowIds
	 */
	public function saveOrder(array $rowIds) {
		$position = 0;

		foreach ($rowIds as $rowId) {
			$payment = $this->paymentRepository->findById($rowId);

			if ($payment instanceof Payment) {
				$payment->setPosition($position);
			}

			$position++;
		}

		$this->em->flush();
	}

}
