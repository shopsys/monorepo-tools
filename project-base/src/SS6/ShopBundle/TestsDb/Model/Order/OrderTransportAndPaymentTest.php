<?php

namespace SS6\ShopBundle\TestsDb\Model\Order;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentData;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportData;
use SS6\ShopBundle\Model\Transport\TransportDomain;

class OrderTransportAndPaymentTest extends DatabaseTestCase {

	public function testVisibilityOfTransportsAndPayments() {
		$em = $this->getEntityManager();

		$vat = new Vat(new VatData('vat', 21));
		$transport1 = new Transport(new TransportData('name', 0, $vat, 'description', false));
		$transport2 = new Transport(new TransportData('name', 0, $vat, 'description', false));
		$transport3 = new Transport(new TransportData('name', 0, $vat, 'description', false));
		$transport4 = new Transport(new TransportData('name', 0, $vat, 'description', false));
		$transportDomain1 = new TransportDomain($transport1, 1);
		$transportDomain2 = new TransportDomain($transport4, 2);
		$transportDomain3 = new TransportDomain($transport4, 3);
		$payment1 = new Payment(new PaymentData('name', 0, $vat, 'description', false));
		$payment2 = new Payment(new PaymentData('name', 0, $vat, 'description', true));
		$payment1->addTransport($transport1);
		$payment1->addTransport($transport4);
		$payment2->addTransport($transport2);

		$em->persist($vat);
		$em->persist($transport1);
		$em->persist($transport2);
		$em->persist($transport3);
		$em->persist($transport4);
		$em->flush();
		$em->persist($transportDomain1);
		$em->persist($transportDomain2);
		$em->persist($transportDomain3);
		$em->persist($payment1);
		$em->persist($payment2);
		$em->flush();

		$transportEditFacade = $this->getContainer()->get('ss6.shop.transport.transport_edit_facade');
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
		$paymentEditFacade = $this->getContainer()->get('ss6.shop.payment.payment_edit_facade');
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */

		$payments = $paymentEditFacade->getVisibleOnCurrentDomain();
		$transports = $transportEditFacade->getVisibleOnCurrentDomain($payments);

		$this->assertContains($payment1, $payments);
		$this->assertNotContains($payment2, $payments);

		$this->assertContains($transport1, $transports);
		$this->assertNotContains($transport2, $transports);
		$this->assertNotContains($transport3, $transports);
		$this->assertNotContains($transport4, $transports);
	}

}
