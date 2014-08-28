<?php

namespace SS6\ShopBundle\Tests\Model\Payment;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentData;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportData;

class PaymentTest extends PHPUnit_Framework_TestCase {
	
	public function testIsVisibleWithoutTransports() {
		$vat = new Vat('vat', 21);
		$payment = new Payment(new PaymentData('name', 0, $vat, 'description', false));
		$this->assertFalse($payment->isVisible());
	}

	public function transportAndPaymentHiddenProvider() {
		return array(
			array('payment' => false, 'transport' => false, 'result' => true),
			array('payment' => false, 'transport' => true, 'result' => false),
			array('payment' => true, 'transport' => false, 'result' => false),
			array('payment' => true, 'transport' => true, 'result' => false),
		);
	}

	/**
	 * @dataProvider transportAndPaymentHiddenProvider
	 */
	public function testIsVisible($paymentHidden, $transportHidden, $resultVisibility) {
		$vat = new Vat('vat', 21);
		$transport = new Transport(new TransportData('name', 0, $vat, 'description', $transportHidden));
		$payment = new Payment(new PaymentData('name', 0, $vat, 'description', $paymentHidden));
		$payment->addTransport($transport);
		$this->assertEquals($resultVisibility, $payment->isVisible());
	}
}
