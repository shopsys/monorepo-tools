<?php

namespace Tests\ShopBundle\Database\Model\Order;

use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Payment\PaymentDataFactory;
use Shopsys\FrameworkBundle\Model\Payment\PaymentFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Transport\Transport;
use Shopsys\FrameworkBundle\Model\Transport\TransportDataFactory;
use Shopsys\FrameworkBundle\Model\Transport\TransportFacade;
use Tests\ShopBundle\Test\DatabaseTestCase;

class OrderTransportAndPaymentTest extends DatabaseTestCase
{
//    public function testVisibleTransport()
//    {
//        $em = $this->getEntityManager();
//        $vat = $this->getDefaultVat();
//
//        $enabledForDomains = [
//            1 => true,
//            2 => false,
//        ];
//        $transport = $this->getDefaultTransport($vat, $enabledForDomains, false);
//        $payment = $this->getDefaultPayment($vat, $enabledForDomains, false);
//
//        $payment->addTransport($transport);
//
//        $em->persist($vat);
//        $em->persist($transport);
//        $em->flush();
//        $em->persist($payment);
//        $em->flush();
//
//        $transportFacade = $this->getContainer()->get(TransportFacade::class);
//        /* @var $transportFacade \Shopsys\FrameworkBundle\Model\Transport\TransportFacade */
//        $paymentFacade = $this->getContainer()->get(PaymentFacade::class);
//        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */
//
//        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();
//        $visibleTransports = $transportFacade->getVisibleOnCurrentDomain($visiblePayments);
//
//        $this->assertContains($transport, $visibleTransports);
//    }

    public function testVisibleTransportHiddenTransport()
    {
        $em = $this->getEntityManager();
        $vat = $this->getDefaultVat();

        $enabledOnDomains = [
            1 => true,
            2 => false,
        ];
        $transport = $this->getDefaultTransport($vat, $enabledOnDomains, true);
        $payment = $this->getDefaultPayment($vat, $enabledOnDomains, false);

        $payment->addTransport($transport);

        $em->persist($vat);
        $em->persist($transport);
        $em->persist($payment);
        $em->flush();

        $transportFacade = $this->getContainer()->get(TransportFacade::class);
        /* @var $transportFacade \Shopsys\FrameworkBundle\Model\Transport\TransportFacade */
        $paymentFacade = $this->getContainer()->get(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();
        $visibleTransports = $transportFacade->getVisibleOnCurrentDomain($visiblePayments);

        $this->assertNotContains($transport, $visibleTransports);
    }

    public function testVisibleTransportHiddenPayment()
    {
        $em = $this->getEntityManager();
        $vat = $this->getDefaultVat();

        $transportEnabledForDomains = [
            1 => true,
            2 => false,
        ];
        $paymentEnabledForDomains = [
            1 => false,
            2 => false,
        ];

        $transport = $this->getDefaultTransport($vat, $transportEnabledForDomains, false);
        $payment = $this->getDefaultPayment($vat, $paymentEnabledForDomains, true);

        $payment->addTransport($transport);

        $em->persist($vat);
        $em->persist($transport);
        $em->persist($payment);
        $em->flush();

        $transportFacade = $this->getContainer()->get(TransportFacade::class);
        /* @var $transportFacade \Shopsys\FrameworkBundle\Model\Transport\TransportFacade */
        $paymentFacade = $this->getContainer()->get(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();
        $visibleTransports = $transportFacade->getVisibleOnCurrentDomain($visiblePayments);

        $this->assertNotContains($transport, $visibleTransports);
    }

    public function testVisibleTransportNoPayment()
    {
        $em = $this->getEntityManager();
        $vat = $this->getDefaultVat();

        $enabledForDomains = [
            1 => true,
            2 => false,
        ];
        $transport = $this->getDefaultTransport($vat, $enabledForDomains, false);

        $em->persist($vat);
        $em->persist($transport);
        $em->flush();

        $transportFacade = $this->getContainer()->get(TransportFacade::class);
        /* @var $transportFacade \Shopsys\FrameworkBundle\Model\Transport\TransportFacade */
        $paymentFacade = $this->getContainer()->get(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();
        $visibleTransports = $transportFacade->getVisibleOnCurrentDomain($visiblePayments);

        $this->assertNotContains($transport, $visibleTransports);
    }

    public function testVisibleTransportOnDifferentDomain()
    {
        $em = $this->getEntityManager();
        $vat = $this->getDefaultVat();

        $paymentEnabledForDomains = [
            1 => true,
            2 => false,
        ];
        $transportEnabledForDomains = [
            1 => false,
            2 => true,
        ];

        $transport = $this->getDefaultTransport($vat, $transportEnabledForDomains, false);
        $payment = $this->getDefaultPayment($vat, $paymentEnabledForDomains, false);

        $payment->addTransport($transport);

        $em->persist($vat);
        $em->persist($transport);
        $em->persist($payment);
        $em->flush();

        $transportFacade = $this->getContainer()->get(TransportFacade::class);
        /* @var $transportFacade \Shopsys\FrameworkBundle\Model\Transport\TransportFacade */
        $paymentFacade = $this->getContainer()->get(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();
        $visibleTransports = $transportFacade->getVisibleOnCurrentDomain($visiblePayments);

        $this->assertNotContains($transport, $visibleTransports);
    }

    public function testVisibleTransportPaymentOnDifferentDomain()
    {
        $em = $this->getEntityManager();
        $vat = $this->getDefaultVat();

        $paymentEnabledForDomains = [
            1 => false,
            2 => true,
        ];
        $transportEnabledForDomains = [
            1 => true,
            2 => false,
        ];
        $transport = $this->getDefaultTransport($vat, $transportEnabledForDomains, false);
        $payment = $this->getDefaultPayment($vat, $paymentEnabledForDomains, false);
        $payment->addTransport($transport);

        $em->persist($vat);
        $em->persist($transport);
        $em->persist($payment);
        $em->flush();

        $transportFacade = $this->getContainer()->get(TransportFacade::class);
        /* @var $transportFacade \Shopsys\FrameworkBundle\Model\Transport\TransportFacade */
        $paymentFacade = $this->getContainer()->get(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();
        $visibleTransports = $transportFacade->getVisibleOnCurrentDomain($visiblePayments);

        $this->assertNotContains($transport, $visibleTransports);
    }

    public function testVisiblePayment()
    {
        $em = $this->getEntityManager();
        $vat = $this->getDefaultVat();

        $enabledForDomains = [
            1 => true,
            2 => false,
        ];
        $transport = $this->getDefaultTransport($vat, $enabledForDomains, false);
        $payment = $this->getDefaultPayment($vat, $enabledForDomains, false);

        $payment->addTransport($transport);

        $em->persist($vat);
        $em->persist($transport);
        $em->persist($payment);
        $em->flush();

        $paymentFacade = $this->getContainer()->get(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();

        $this->assertContains($payment, $visiblePayments);
    }

    public function testVisiblePaymentHiddenTransport()
    {
        $em = $this->getEntityManager();
        $vat = $this->getDefaultVat();

        $enabledForDomains = [
            1 => true,
            2 => false,
        ];
        $transport = $this->getDefaultTransport($vat, $enabledForDomains, true);
        $payment = $this->getDefaultPayment($vat, $enabledForDomains, false);

        $payment->addTransport($transport);

        $em->persist($vat);
        $em->persist($transport);
        $em->persist($payment);
        $em->flush();

        $paymentFacade = $this->getContainer()->get(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();

        $this->assertNotContains($payment, $visiblePayments);
    }

    public function testVisiblePaymentHiddenPayment()
    {
        $em = $this->getEntityManager();
        $vat = $this->getDefaultVat();

        $enabledForDomains = [
            1 => true,
            2 => false,
        ];
        $transport = $this->getDefaultTransport($vat, $enabledForDomains, false);
        $payment = $this->getDefaultPayment($vat, $enabledForDomains, true);

        $payment->addTransport($transport);

        $em->persist($vat);
        $em->persist($transport);
        $em->persist($payment);
        $em->flush();

        $paymentFacade = $this->getContainer()->get(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();

        $this->assertNotContains($payment, $visiblePayments);
    }

    public function testVisiblePaymentNoTransport()
    {
        $em = $this->getEntityManager();
        $vat = $this->getDefaultVat();

        $enabledForDomains = [
            1 => true,
            2 => false,
        ];
        $payment = $this->getDefaultPayment($vat, $enabledForDomains, false);

        $em->persist($vat);
        $em->persist($payment);
        $em->flush();

        $paymentFacade = $this->getContainer()->get(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();

        $this->assertNotContains($payment, $visiblePayments);
    }

    public function testVisiblePaymentOnDifferentDomain()
    {
        $em = $this->getEntityManager();
        $vat = $this->getDefaultVat();

        $transportEnabledForDomains = [
            1 => true,
            2 => false,
        ];
        $paymentEnabledForDomains = [
            1 => false,
            2 => true,
        ];
        $transport = $this->getDefaultTransport($vat, $transportEnabledForDomains, false);
        $payment = $this->getDefaultPayment($vat, $paymentEnabledForDomains, false);
        $payment->addTransport($transport);

        $em->persist($vat);
        $em->persist($transport);
        $em->persist($payment);
        $em->flush();

        $paymentFacade = $this->getContainer()->get(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();

        $this->assertNotContains($payment, $visiblePayments);
    }

    public function testVisiblePaymentTransportOnDifferentDomain()
    {
        $em = $this->getEntityManager();
        $vat = $this->getDefaultVat();

        $transportEnabledForDomains = [
            1 => true,
            2 => false,
        ];
        $paymentEnabledForDomains = [
            1 => false,
            2 => true,
        ];
        $transport = $this->getDefaultTransport($vat, $transportEnabledForDomains, false);
        $payment = $this->getDefaultPayment($vat, $paymentEnabledForDomains, false);

        $payment->addTransport($transport);

        $em->persist($vat);
        $em->persist($transport);
        $em->persist($payment);
        $em->flush();

        $paymentFacade = $this->getContainer()->get(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $visiblePayments = $paymentFacade->getVisibleOnCurrentDomain();

        $this->assertNotContains($payment, $visiblePayments);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     * @param bool[] $enabledForDomains
     * @param bool $hidden
     * @return Payment
     */
    public function getDefaultPayment(Vat $vat, $enabledForDomains, $hidden)
    {
        $paymentDataFactory = $this->getPaymentDataFactory();

        $paymentData = $paymentDataFactory->create();
        $paymentData->name = [
            'cs' => 'paymentName',
            'en' => 'paymentName',
        ];
        $paymentData->vat = $vat;
        $paymentData->hidden = $hidden;
        $paymentData->enabled = $enabledForDomains;

        return new Payment($paymentData);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     * @param bool[] $enabledForDomains
     * @param bool $hidden
     * @return \Shopsys\FrameworkBundle\Model\Transport\Transport
     */
    public function getDefaultTransport(Vat $vat, $enabledForDomains, $hidden)
    {
        $transportDataFactory = $this->getTransportDataFactory();

        $transportData = $transportDataFactory->createDefault();
        $transportData->name = [
            'cs' => 'paymentName',
            'en' => 'paymentName',
        ];

        $transportData->vat = $vat;
        $transportData->hidden = $hidden;
        $transportData->enabled = $enabledForDomains;

        return new Transport($transportData);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat
     */
    private function getDefaultVat()
    {
        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = 21;
        return new Vat($vatData);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\PaymentDataFactory
     */
    public function getPaymentDataFactory()
    {
        return $this->getContainer()->get(PaymentDataFactory::class);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Transport\TransportDataFactory
     */
    public function getTransportDataFactory()
    {
        return $this->getContainer()->get(TransportDataFactory::class);
    }
}
