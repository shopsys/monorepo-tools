<?php

namespace SS6\ShopBundle\TestsDb\Model\Payment;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Transport\Transport;

class PaymentTest extends DatabaseTestCase {
	
	public function testIsVisibleAfterDeleteTransport() {
		$em = $this->getEntityManager();

		$transport = new Transport('name', 0, 'description', false);
		$payment = new Payment('name', 0, 'description', false);
		$payment->addTransport($transport);

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
