<?php

namespace Tests\ShopBundle\Database\Model\Order;

use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Payment\PaymentData;
use Shopsys\FrameworkBundle\Model\Payment\PaymentDomain;
use Shopsys\FrameworkBundle\Model\Payment\PaymentFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Transport\Transport;
use Shopsys\FrameworkBundle\Model\Transport\TransportData;
use Shopsys\FrameworkBundle\Model\Transport\TransportDomain;
use Shopsys\FrameworkBundle\Model\Transport\TransportFacade;
use Tests\ShopBundle\Test\DatabaseTestCase;

class OrderTransportAndPaymentTest extends DatabaseTestCase
{
    public function testVisibleTransport()
    {
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

        $transportFacade = $this->getServiceByType(TransportFacade::class);
        /* @var $transportFacade \Shopsys\FrameworkBundle\Model\Transport\TransportFacade */
        $paymentFacade = $this->getServiceByType(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();
        $visibleTransports = $transportFacade->getVisibleOnCurrentDomain($visiblePayments);

        $this->assertContains($transport, $visibleTransports);
    }

    public function testVisibleTransportHiddenTransport()
    {
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

        $transportFacade = $this->getServiceByType(TransportFacade::class);
        /* @var $transportFacade \Shopsys\FrameworkBundle\Model\Transport\TransportFacade */
        $paymentFacade = $this->getServiceByType(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();
        $visibleTransports = $transportFacade->getVisibleOnCurrentDomain($visiblePayments);

        $this->assertNotContains($transport, $visibleTransports);
    }

    public function testVisibleTransportHiddenPayment()
    {
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

        $transportFacade = $this->getServiceByType(TransportFacade::class);
        /* @var $transportFacade \Shopsys\FrameworkBundle\Model\Transport\TransportFacade */
        $paymentFacade = $this->getServiceByType(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();
        $visibleTransports = $transportFacade->getVisibleOnCurrentDomain($visiblePayments);

        $this->assertNotContains($transport, $visibleTransports);
    }

    public function testVisibleTransportNoPayment()
    {
        $em = $this->getEntityManager();

        $vat = new Vat(new VatData('vat', 21));
        $transport = new Transport(new TransportData(['cs' => 'transportName', 'en' => 'transportName'], $vat, [], [], false));
        $transportDomain = new TransportDomain($transport, 1);

        $em->persist($vat);
        $em->persist($transport);
        $em->flush();
        $em->persist($transportDomain);
        $em->flush();

        $transportFacade = $this->getServiceByType(TransportFacade::class);
        /* @var $transportFacade \Shopsys\FrameworkBundle\Model\Transport\TransportFacade */
        $paymentFacade = $this->getServiceByType(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();
        $visibleTransports = $transportFacade->getVisibleOnCurrentDomain($visiblePayments);

        $this->assertNotContains($transport, $visibleTransports);
    }

    public function testVisibleTransportOnDifferentDomain()
    {
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

        $transportFacade = $this->getServiceByType(TransportFacade::class);
        /* @var $transportFacade \Shopsys\FrameworkBundle\Model\Transport\TransportFacade */
        $paymentFacade = $this->getServiceByType(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();
        $visibleTransports = $transportFacade->getVisibleOnCurrentDomain($visiblePayments);

        $this->assertNotContains($transport, $visibleTransports);
    }

    public function testVisibleTransportPaymentOnDifferentDomain()
    {
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

        $transportFacade = $this->getServiceByType(TransportFacade::class);
        /* @var $transportFacade \Shopsys\FrameworkBundle\Model\Transport\TransportFacade */
        $paymentFacade = $this->getServiceByType(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();
        $visibleTransports = $transportFacade->getVisibleOnCurrentDomain($visiblePayments);

        $this->assertNotContains($transport, $visibleTransports);
    }

    public function testVisiblePayment()
    {
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

        $paymentFacade = $this->getServiceByType(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();

        $this->assertContains($payment, $visiblePayments);
    }

    public function testVisiblePaymentHiddenTransport()
    {
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

        $paymentFacade = $this->getServiceByType(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();

        $this->assertNotContains($payment, $visiblePayments);
    }

    public function testVisiblePaymentHiddenPayment()
    {
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

        $paymentFacade = $this->getServiceByType(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();

        $this->assertNotContains($payment, $visiblePayments);
    }

    public function testVisiblePaymentNoTransport()
    {
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

        $paymentFacade = $this->getServiceByType(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();

        $this->assertNotContains($payment, $visiblePayments);
    }

    public function testVisiblePaymentOnDifferentDomain()
    {
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

        $paymentFacade = $this->getServiceByType(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();

        $this->assertNotContains($payment, $visiblePayments);
    }

    public function testVisiblePaymentTransportOnDifferentDomain()
    {
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

        $paymentFacade = $this->getServiceByType(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();

        $this->assertNotContains($payment, $visiblePayments);
    }
}
