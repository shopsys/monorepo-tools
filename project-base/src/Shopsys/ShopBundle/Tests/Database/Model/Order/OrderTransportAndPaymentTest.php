<?php

namespace SS6\ShopBundle\Tests\Database\Model\Order;

use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentData;
use SS6\ShopBundle\Model\Payment\PaymentDomain;
use SS6\ShopBundle\Model\Payment\PaymentEditFacade;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportData;
use SS6\ShopBundle\Model\Transport\TransportDomain;
use SS6\ShopBundle\Model\Transport\TransportEditFacade;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

class OrderTransportAndPaymentTest extends DatabaseTestCase {

	public function testVisibleTransport() {
		$em = $this->getEntityManager();

		$vat = new Vat(new VatData('vat', 21));
		$transport = new Transport(new TransportData(['cs' => 'transportName', 'en' => 'transportName'], $vat, [], [], false));
		$transportDomain = new TransportDomain($transport, 1);
		$payment = new Payment(new PaymentData(['cs' => 'paymentName', 'en' => 'paymentName'], $vat, [], [], false));
		$paymentDomain = new PaymentDomain($payment, 1);
		$payment->addTransport($transport);

		$em->persist($vat);
		$em->persist($transport);
		$em->flush();
		$em->persist($transportDomain);
		$em->persist($payment);
		$em->flush();
		$em->persist($paymentDomain);
		$em->flush();

		$transportEditFacade = $this->getContainer()->get(TransportEditFacade::class);
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
		$paymentEditFacade = $this->getContainer()->get(PaymentEditFacade::class);
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */

		$visiblePayments = $paymentEditFacade->getVisibleOnCurrentDomain();
		$visibleTransports = $transportEditFacade->getVisibleOnCurrentDomain($visiblePayments);

		$this->assertContains($transport, $visibleTransports);
	}

	public function testVisibleTransportHiddenTransport() {
		$em = $this->getEntityManager();

		$vat = new Vat(new VatData('vat', 21));
		$transport = new Transport(new TransportData(['cs' => 'transportName', 'en' => 'transportName'], $vat, [], [], true));
		$transportDomain = new TransportDomain($transport, 1);
		$payment = new Payment(new PaymentData(['cs' => 'paymentName', 'en' => 'paymentName'], $vat, [], [], false));
		$paymentDomain = new PaymentDomain($payment, 1);
		$payment->addTransport($transport);

		$em->persist($vat);
		$em->persist($transport);
		$em->flush();
		$em->persist($transportDomain);
		$em->persist($payment);
		$em->flush();
		$em->persist($paymentDomain);
		$em->flush();

		$transportEditFacade = $this->getContainer()->get(TransportEditFacade::class);
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
		$paymentEditFacade = $this->getContainer()->get(PaymentEditFacade::class);
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */

		$visiblePayments = $paymentEditFacade->getVisibleOnCurrentDomain();
		$visibleTransports = $transportEditFacade->getVisibleOnCurrentDomain($visiblePayments);

		$this->assertNotContains($transport, $visibleTransports);
	}

	public function testVisibleTransportHiddenPayment() {
		$em = $this->getEntityManager();

		$vat = new Vat(new VatData('vat', 21));
		$transport = new Transport(new TransportData(['cs' => 'transportName', 'en' => 'transportName'], $vat, [], [], false));
		$transportDomain = new TransportDomain($transport, 1);
		$payment = new Payment(new PaymentData(['cs' => 'paymentName', 'en' => 'paymentName'], $vat, [], [], true));
		$paymentDomain = new PaymentDomain($payment, 1);
		$payment->addTransport($transport);

		$em->persist($vat);
		$em->persist($transport);
		$em->flush();
		$em->persist($transportDomain);
		$em->persist($payment);
		$em->flush();
		$em->persist($paymentDomain);
		$em->flush();

		$transportEditFacade = $this->getContainer()->get(TransportEditFacade::class);
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
		$paymentEditFacade = $this->getContainer()->get(PaymentEditFacade::class);
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */

		$visiblePayments = $paymentEditFacade->getVisibleOnCurrentDomain();
		$visibleTransports = $transportEditFacade->getVisibleOnCurrentDomain($visiblePayments);

		$this->assertNotContains($transport, $visibleTransports);
	}

	public function testVisibleTransportNoPayment() {
		$em = $this->getEntityManager();

		$vat = new Vat(new VatData('vat', 21));
		$transport = new Transport(new TransportData(['cs' => 'transportName', 'en' => 'transportName'], $vat, [], [], false));
		$transportDomain = new TransportDomain($transport, 1);

		$em->persist($vat);
		$em->persist($transport);
		$em->flush();
		$em->persist($transportDomain);
		$em->flush();

		$transportEditFacade = $this->getContainer()->get(TransportEditFacade::class);
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
		$paymentEditFacade = $this->getContainer()->get(PaymentEditFacade::class);
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */

		$visiblePayments = $paymentEditFacade->getVisibleOnCurrentDomain();
		$visibleTransports = $transportEditFacade->getVisibleOnCurrentDomain($visiblePayments);

		$this->assertNotContains($transport, $visibleTransports);
	}

	public function testVisibleTransportOnDifferentDomain() {
		$em = $this->getEntityManager();

		$vat = new Vat(new VatData('vat', 21));
		$transport = new Transport(new TransportData(['cs' => 'transportName', 'en' => 'transportName'], $vat, [], [], false));
		$transportDomain = new TransportDomain($transport, 2);
		$payment = new Payment(new PaymentData(['cs' => 'paymentName', 'en' => 'paymentName'], $vat, [], [], false));
		$paymentDomain = new PaymentDomain($payment, 1);
		$payment->addTransport($transport);

		$em->persist($vat);
		$em->persist($transport);
		$em->flush();
		$em->persist($transportDomain);
		$em->persist($payment);
		$em->flush();
		$em->persist($paymentDomain);
		$em->flush();

		$transportEditFacade = $this->getContainer()->get(TransportEditFacade::class);
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
		$paymentEditFacade = $this->getContainer()->get(PaymentEditFacade::class);
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */

		$visiblePayments = $paymentEditFacade->getVisibleOnCurrentDomain();
		$visibleTransports = $transportEditFacade->getVisibleOnCurrentDomain($visiblePayments);

		$this->assertNotContains($transport, $visibleTransports);
	}

	public function testVisibleTransportPaymentOnDifferentDomain() {
		$em = $this->getEntityManager();

		$vat = new Vat(new VatData('vat', 21));
		$transport = new Transport(new TransportData(['cs' => 'transportName', 'en' => 'transportName'], $vat, [], [], false));
		$transportDomain = new TransportDomain($transport, 1);
		$payment = new Payment(new PaymentData(['cs' => 'paymentName', 'en' => 'paymentName'], $vat, [], [], false));
		$paymentDomain = new PaymentDomain($payment, 2);
		$payment->addTransport($transport);

		$em->persist($vat);
		$em->persist($transport);
		$em->flush();
		$em->persist($transportDomain);
		$em->persist($payment);
		$em->flush();
		$em->persist($paymentDomain);
		$em->flush();

		$transportEditFacade = $this->getContainer()->get(TransportEditFacade::class);
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
		$paymentEditFacade = $this->getContainer()->get(PaymentEditFacade::class);
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */

		$visiblePayments = $paymentEditFacade->getVisibleOnCurrentDomain();
		$visibleTransports = $transportEditFacade->getVisibleOnCurrentDomain($visiblePayments);

		$this->assertNotContains($transport, $visibleTransports);
	}

	public function testVisiblePayment() {
		$em = $this->getEntityManager();

		$vat = new Vat(new VatData('vat', 21));
		$transport = new Transport(new TransportData(['cs' => 'transportName', 'en' => 'transportName'], $vat, [], [], false));
		$transportDomain = new TransportDomain($transport, 1);
		$payment = new Payment(new PaymentData(['cs' => 'paymentName', 'en' => 'paymentName'], $vat, [], [], false));
		$paymentDomain = new PaymentDomain($payment, 1);
		$payment->addTransport($transport);

		$em->persist($vat);
		$em->persist($transport);
		$em->flush();
		$em->persist($transportDomain);
		$em->persist($payment);
		$em->flush();
		$em->persist($paymentDomain);
		$em->flush();

		$paymentEditFacade = $this->getContainer()->get(PaymentEditFacade::class);
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */

		$visiblePayments = $paymentEditFacade->getVisibleOnCurrentDomain();

		$this->assertContains($payment, $visiblePayments);
	}

	public function testVisiblePaymentHiddenTransport() {
		$em = $this->getEntityManager();

		$vat = new Vat(new VatData('vat', 21));
		$transport = new Transport(new TransportData(['cs' => 'transportName', 'en' => 'transportName'], $vat, [], [], true));
		$transportDomain = new TransportDomain($transport, 1);
		$payment = new Payment(new PaymentData(['cs' => 'paymentName', 'en' => 'paymentName'], $vat, [], [], false));
		$paymentDomain = new PaymentDomain($payment, 1);
		$payment->addTransport($transport);

		$em->persist($vat);
		$em->persist($transport);
		$em->flush();
		$em->persist($transportDomain);
		$em->persist($payment);
		$em->flush();
		$em->persist($paymentDomain);
		$em->flush();

		$paymentEditFacade = $this->getContainer()->get(PaymentEditFacade::class);
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */

		$visiblePayments = $paymentEditFacade->getVisibleOnCurrentDomain();

		$this->assertNotContains($payment, $visiblePayments);
	}

	public function testVisiblePaymentHiddenPayment() {
		$em = $this->getEntityManager();

		$vat = new Vat(new VatData('vat', 21));
		$transport = new Transport(new TransportData(['cs' => 'transportName', 'en' => 'transportName'], $vat, [], [], false));
		$transportDomain = new TransportDomain($transport, 1);
		$payment = new Payment(new PaymentData(['cs' => 'paymentName', 'en' => 'paymentName'], $vat, [], [], true));
		$paymentDomain = new PaymentDomain($payment, 1);
		$payment->addTransport($transport);

		$em->persist($vat);
		$em->persist($transport);
		$em->flush();
		$em->persist($transportDomain);
		$em->persist($payment);
		$em->flush();
		$em->persist($paymentDomain);
		$em->flush();

		$paymentEditFacade = $this->getContainer()->get(PaymentEditFacade::class);
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */

		$visiblePayments = $paymentEditFacade->getVisibleOnCurrentDomain();

		$this->assertNotContains($payment, $visiblePayments);
	}

	public function testVisiblePaymentNoTransport() {
		$em = $this->getEntityManager();

		$vat = new Vat(new VatData('vat', 21));
		$payment = new Payment(new PaymentData(['cs' => 'paymentName', 'en' => 'paymentName'], $vat, [], [], false));
		$paymentDomain = new PaymentDomain($payment, 1);

		$em->persist($vat);
		$em->flush();
		$em->persist($payment);
		$em->flush();
		$em->persist($paymentDomain);
		$em->flush();

		$paymentEditFacade = $this->getContainer()->get(PaymentEditFacade::class);
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */

		$visiblePayments = $paymentEditFacade->getVisibleOnCurrentDomain();

		$this->assertNotContains($payment, $visiblePayments);
	}

	public function testVisiblePaymentOnDifferentDomain() {
		$em = $this->getEntityManager();

		$vat = new Vat(new VatData('vat', 21));
		$transport = new Transport(new TransportData(['cs' => 'transportName', 'en' => 'transportName'], $vat, [], [], false));
		$transportDomain = new TransportDomain($transport, 1);
		$payment = new Payment(new PaymentData(['cs' => 'paymentName', 'en' => 'paymentName'], $vat, [], [], false));
		$paymentDomain = new PaymentDomain($payment, 2);
		$payment->addTransport($transport);

		$em->persist($vat);
		$em->persist($transport);
		$em->flush();
		$em->persist($transportDomain);
		$em->persist($payment);
		$em->flush();
		$em->persist($paymentDomain);
		$em->flush();

		$paymentEditFacade = $this->getContainer()->get(PaymentEditFacade::class);
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */

		$visiblePayments = $paymentEditFacade->getVisibleOnCurrentDomain();

		$this->assertNotContains($payment, $visiblePayments);
	}

	public function testVisiblePaymentTransportOnDifferentDomain() {
		$em = $this->getEntityManager();

		$vat = new Vat(new VatData('vat', 21));
		$transport = new Transport(new TransportData(['cs' => 'transportName', 'en' => 'transportName'], $vat, [], [], false));
		$transportDomain = new TransportDomain($transport, 2);
		$payment = new Payment(new PaymentData(['cs' => 'paymentName', 'en' => 'paymentName'], $vat, [], [], false));
		$paymentDomain = new PaymentDomain($payment, 1);
		$payment->addTransport($transport);

		$em->persist($vat);
		$em->persist($transport);
		$em->flush();
		$em->persist($transportDomain);
		$em->persist($payment);
		$em->flush();
		$em->persist($paymentDomain);
		$em->flush();

		$paymentEditFacade = $this->getContainer()->get(PaymentEditFacade::class);
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */

		$visiblePayments = $paymentEditFacade->getVisibleOnCurrentDomain();

		$this->assertNotContains($payment, $visiblePayments);
	}

}
