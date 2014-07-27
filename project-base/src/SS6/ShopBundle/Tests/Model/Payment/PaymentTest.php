<?php

namespace SS6\ShopBundle\Tests\Model\Payment;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Transport\Transport;

class PaymentTest extends PHPUnit_Framework_TestCase {
	
	public function testIsVisibleWithoutTransports() {
		$payment = new Payment('name', 0, 'description', false);
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
		$transport = new Transport('name', 0, 'description', $transportHidden);
		$payment = new Payment('name', 0, 'description', $paymentHidden);
		$payment->addTransport($transport);
		$this->assertEquals($resultVisibility, $payment->isVisible());
	}
}
