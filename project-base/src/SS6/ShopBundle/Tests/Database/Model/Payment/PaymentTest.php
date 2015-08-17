<?php

namespace SS6\ShopBundle\Tests\Database\Model\Payment;

use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentData;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportData;
use SS6\ShopBundle\Model\Transport\TransportEditFacade;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

class PaymentTest extends DatabaseTestCase {

	public function testRemoveTransportFromPaymentAfterDelete() {
		$em = $this->getEntityManager();

		$vat = new Vat(new VatData('vat', 21));
		$transport = new Transport(new TransportData([], $vat, [], [], false));
		$payment = new Payment(new PaymentData(['cs' => 'name'], $vat, [], [], false));
		$payment->addTransport($transport);

		$em->persist($vat);
		$em->persist($transport);
		$em->persist($payment);
		$em->flush();

		$transportEditFacade = $this->getContainer()->get(TransportEditFacade::class);
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
		$transportEditFacade->deleteById($transport->getId());

		$this->assertFalse($payment->getTransports()->contains($transport));
	}
}
