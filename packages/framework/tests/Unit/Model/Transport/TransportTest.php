<?php

namespace Tests\FrameworkBundle\Unit\Model\Transport;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Payment\PaymentData;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Transport\Transport;
use Shopsys\FrameworkBundle\Model\Transport\TransportData;

class TransportTest extends TestCase
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Transport\Transport
     */
    private function createTransport()
    {
        $vat = new Vat(new VatData('vat', 21));
        $transportData = new TransportData(['cs' => 'transportName', 'en' => 'transportName'], $vat, [], [], false);
        $transport = new Transport($transportData);

        return $transport;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment
     */
    private function createPayment()
    {
        $vat = new Vat(new VatData('vat', 21));
        $paymentData = new PaymentData();
        $paymentData->name = ['cs' => 'paymentName', 'en' => 'paymentName'];
        $paymentData->vat = $vat;
        $paymentData->hidden = true;
        $payment = new Payment($paymentData);

        return $payment;
    }

    public function testSetPayments()
    {
        $transport = $this->createTransport();
        $payment = $this->createPayment();
        $transport->setPayments([$payment]);

        $this->assertContains($payment, $transport->getPayments());
        $this->assertContains($transport, $payment->getTransports());
    }

    public function testRemovePayment()
    {
        $transport = $this->createTransport();
        $payment = $this->createPayment();
        $transport->setPayments([$payment]);
        $transport->removePayment($payment);

        $this->assertNotContains($payment, $transport->getPayments());
        $this->assertNotContains($transport, $payment->getTransports());
    }
}
