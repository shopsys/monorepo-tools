<?php

namespace SS6\ShopBundle\TestsDb\Model\Payment;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentData;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportData;

class PaymentTest extends DatabaseTestCase {
	
	public function testIsVisibleAfterDeleteTransport() {
		$em = $this->getEntityManager();

		$vat = new Vat(new VatData('vat', 21));
		$transport = new Transport(new TransportData('name', 0, $vat, 'description', false));
		$payment = new Payment(new PaymentData('name', 0, $vat, 'description', false));
		$payment->addTransport($transport);

		$em->persist($vat);
		$em->persist($transport);
		$em->persist($payment);
		$em->flush();
		$this->assertTrue($payment->isVisible());

		$transportEditFacade = $this->getContainer()->get('ss6.shop.transport.transport_edit_facade');
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
		$transportEditFacade->deleteById($transport->getId());

		$this->assertFalse($payment->isVisible());
	}
}
