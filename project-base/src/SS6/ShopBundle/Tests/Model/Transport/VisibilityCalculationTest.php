<?php

namespace SS6\ShopBundle\TestsDb\Model\Payment;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentData;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportData;
use SS6\ShopBundle\Model\Transport\VisibilityCalculation;

class VisibilityCalculationTest extends PHPUnit_Framework_TestCase {
	
	public function testIsVisible() {
		$vat = new Vat('vat', 21);

		$transport1 = new Transport(new TransportData('name', 0, $vat, 'description', false));
		$transport2 = new Transport(new TransportData('name', 0, $vat, 'description', false));
		$transport3 = new Transport(new TransportData('name', 0, $vat, 'description', false));
		$transport4 = new Transport(new TransportData('name', 0, $vat, 'description', true));
		$payment1 = new Payment(new PaymentData('name', 0, $vat, 'description', false));
		$payment2 = new Payment(new PaymentData('name', 0, $vat, 'description', true));
		$payment1->addTransport($transport1);
		$payment1->addTransport($transport4);
		$payment2->addTransport($transport2);
		$allPayments = array($payment1, $payment2);

		$visibilityCalculation = new VisibilityCalculation();

		$this->assertTrue($visibilityCalculation->isVisible($transport1, $allPayments));
		$this->assertFalse($visibilityCalculation->isVisible($transport2, $allPayments));
		$this->assertFalse($visibilityCalculation->isVisible($transport3, $allPayments));
		$this->assertFalse($visibilityCalculation->isVisible($transport4, $allPayments));
	}
}
