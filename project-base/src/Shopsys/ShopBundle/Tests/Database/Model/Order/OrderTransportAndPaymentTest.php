<?php

namespace Shopsys\ShopBundle\Tests\Database\Model\Order;

use Shopsys\ShopBundle\Model\Payment\Payment;
use Shopsys\ShopBundle\Model\Payment\PaymentData;
use Shopsys\ShopBundle\Model\Payment\PaymentDomain;
use Shopsys\ShopBundle\Model\Payment\PaymentFacade;
use Shopsys\ShopBundle\Model\Pricing\Vat\Vat;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatData;
use Shopsys\ShopBundle\Model\Transport\Transport;
use Shopsys\ShopBundle\Model\Transport\TransportData;
use Shopsys\ShopBundle\Model\Transport\TransportDomain;
use Shopsys\ShopBundle\Model\Transport\TransportFacade;
use Shopsys\ShopBundle\Tests\Test\DatabaseTestCase;

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

		$transportFacade = $this->getContainer()->get(TransportFacade::class);
		/* @var $transportFacade \Shopsys\ShopBundle\Model\Transport\TransportFacade */
		$paymentFacade = $this->getContainer()->get(PaymentFacade::class);
		/* @var $paymentFacade \Shopsys\ShopBundle\Model\Payment\PaymentFacade */

		$visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();
		$visibleTransports = $transportFacade->getVisibleOnCurrentDomain($visiblePayments);

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

		$transportFacade = $this->getContainer()->get(TransportFacade::class);
		/* @var $transportFacade \Shopsys\ShopBundle\Model\Transport\TransportFacade */
		$paymentFacade = $this->getContainer()->get(PaymentFacade::class);
		/* @var $paymentFacade \Shopsys\ShopBundle\Model\Payment\PaymentFacade */

		$visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();
		$visibleTransports = $transportFacade->getVisibleOnCurrentDomain($visiblePayments);

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

		$transportFacade = $this->getContainer()->get(TransportFacade::class);
		/* @var $transportFacade \Shopsys\ShopBundle\Model\Transport\TransportFacade */
		$paymentFacade = $this->getContainer()->get(PaymentFacade::class);
		/* @var $paymentFacade \Shopsys\ShopBundle\Model\Payment\PaymentFacade */

		$visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();
		$visibleTransports = $transportFacade->getVisibleOnCurrentDomain($visiblePayments);

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

		$transportFacade = $this->getContainer()->get(TransportFacade::class);
		/* @var $transportFacade \Shopsys\ShopBundle\Model\Transport\TransportFacade */
		$paymentFacade = $this->getContainer()->get(PaymentFacade::class);
		/* @var $paymentFacade \Shopsys\ShopBundle\Model\Payment\PaymentFacade */

		$visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();
		$visibleTransports = $transportFacade->getVisibleOnCurrentDomain($visiblePayments);

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

		$transportFacade = $this->getContainer()->get(TransportFacade::class);
		/* @var $transportFacade \Shopsys\ShopBundle\Model\Transport\TransportFacade */
		$paymentFacade = $this->getContainer()->get(PaymentFacade::class);
		/* @var $paymentFacade \Shopsys\ShopBundle\Model\Payment\PaymentFacade */

		$visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();
		$visibleTransports = $transportFacade->getVisibleOnCurrentDomain($visiblePayments);

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

		$transportFacade = $this->getContainer()->get(TransportFacade::class);
		/* @var $transportFacade \Shopsys\ShopBundle\Model\Transport\TransportFacade */
		$paymentFacade = $this->getContainer()->get(PaymentFacade::class);
		/* @var $paymentFacade \Shopsys\ShopBundle\Model\Payment\PaymentFacade */

		$visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();
		$visibleTransports = $transportFacade->getVisibleOnCurrentDomain($visiblePayments);

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

		$paymentFacade = $this->getContainer()->get(PaymentFacade::class);
		/* @var $paymentFacade \Shopsys\ShopBundle\Model\Payment\PaymentFacade */

		$visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();

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

		$paymentFacade = $this->getContainer()->get(PaymentFacade::class);
		/* @var $paymentFacade \Shopsys\ShopBundle\Model\Payment\PaymentFacade */

		$visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();

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

		$paymentFacade = $this->getContainer()->get(PaymentFacade::class);
		/* @var $paymentFacade \Shopsys\ShopBundle\Model\Payment\PaymentFacade */

		$visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();

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

		$paymentFacade = $this->getContainer()->get(PaymentFacade::class);
		/* @var $paymentFacade \Shopsys\ShopBundle\Model\Payment\PaymentFacade */

		$visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();

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

		$paymentFacade = $this->getContainer()->get(PaymentFacade::class);
		/* @var $paymentFacade \Shopsys\ShopBundle\Model\Payment\PaymentFacade */

		$visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();

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

		$paymentFacade = $this->getContainer()->get(PaymentFacade::class);
		/* @var $paymentFacade \Shopsys\ShopBundle\Model\Payment\PaymentFacade */

		$visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();

		$this->assertNotContains($payment, $visiblePayments);
	}

}
