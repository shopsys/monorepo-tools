<?php

namespace SS6\ShopBundle\TestsDb\Model\Payment;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Transport\Transport;

class TransportRepositoryTest extends DatabaseTestCase {
	
	public function testFindAllDataWithVisibility() {
		$em = $this->getEntityManager();

		$transport1 = new Transport('name', 0, 'description', false);
		$transport2 = new Transport('name', 0, 'description', false);
		$transport3 = new Transport('name', 0, 'description', false);
		$payment1 = new Payment('name', 0, 'description', false);
		$payment2 = new Payment('name', 0, 'description', true);
		$payment1->addTransport($transport1);
		$payment2->addTransport($transport2);

		$em->persist($transport1);
		$em->persist($transport2);
		$em->persist($transport3);
		$em->persist($payment1);
		$em->persist($payment2);
		$em->flush();

		$paymentRepository = $this->getContainer()->get('ss6.shop.payment.payment_repository');
		/* @var $paymentRepository \SS6\ShopBundle\Model\Payment\PaymentRepository */
		$allPayments = $paymentRepository->findAllWithTransports();

		$transportRepository = $this->getContainer()->get('ss6.shop.transport.transport_repository');
		/* @var $transportRepository \SS6\ShopBundle\Model\Transport\TransportRepository */
		$transportsDataWithVisibility = $transportRepository->findAllDataWithVisibility($allPayments);

		foreach ($transportsDataWithVisibility as $row) {
			if ($row['entity']->getId() === $transport1->getId()) {
				$this->assertTrue($row['visible']);
			} elseif ($row['entity']->getId() === $transport2->getId()) {
				$this->assertFalse($row['visible']);
			} elseif ($row['entity']->getId() === $transport3->getId()) {
				$this->assertFalse($row['visible']);
			}
		}
	}
}
