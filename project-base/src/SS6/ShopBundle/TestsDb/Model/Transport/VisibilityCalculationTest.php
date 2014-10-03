<?php

namespace SS6\ShopBundle\TestsDb\Model\Payment;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentData;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportData;

class VisibilityCalculationTest extends DatabaseTestCase {
	
	public function testFindAllVisible() {
		$em = $this->getEntityManager();

		$vat = new Vat(new VatData('vat', 21));
		$transport1 = new Transport(new TransportData('name', 0, $vat, 'description', false));
		$transport2 = new Transport(new TransportData('name', 0, $vat, 'description', false));
		$transport3 = new Transport(new TransportData('name', 0, $vat, 'description', false));
		$payment1 = new Payment(new PaymentData('name', 0, $vat, 'description', false));
		$payment2 = new Payment(new PaymentData('name', 0, $vat, 'description', true));
		$payment1->addTransport($transport1);
		$payment2->addTransport($transport2);

		$em->persist($vat);
		$em->persist($transport1);
		$em->persist($transport2);
		$em->persist($transport3);
		$em->persist($payment1);
		$em->persist($payment2);
		$em->flush();

		$paymentRepository = $this->getContainer()->get('ss6.shop.payment.payment_repository');
		/* @var $paymentRepository \SS6\ShopBundle\Model\Payment\PaymentRepository */

		$transportRepository = $this->getContainer()->get('ss6.shop.transport.transport_repository');
		/* @var $transportRepository \SS6\ShopBundle\Model\Transport\TransportRepository */

		$visibilityCalculation = $this->getContainer()->get('ss6.shop.transport.visibility_calculation');
		/* @var $visibilityCalculation \SS6\ShopBundle\Model\Transport\VisibilityCalculation */
		
		$allPayments = $paymentRepository->findAllWithTransports();
		$transports = $transportRepository->findAll();
		$transportsDataWithVisibility = $visibilityCalculation->findAll($transports, $allPayments);
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
