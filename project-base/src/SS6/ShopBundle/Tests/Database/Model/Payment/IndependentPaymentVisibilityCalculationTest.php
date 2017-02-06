<?php

namespace SS6\ShopBundle\Tests\Database\Model\Payment;

use SS6\ShopBundle\Model\Payment\IndependentPaymentVisibilityCalculation;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentData;
use SS6\ShopBundle\Model\Payment\PaymentDomain;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

class IndependentPaymentVisibilityCalculationTest extends DatabaseTestCase {

	public function testIsIndependentlyVisible() {
		$em = $this->getEntityManager();

		$domainId = 1;
		$vat = new Vat(new VatData('vat', 21));
		$payment = new Payment(new PaymentData(['cs' => 'name'], $vat, [], [], false));

		$em->persist($vat);
		$em->persist($payment);
		$em->flush();

		$paymentDomain = new PaymentDomain($payment, $domainId);
		$em->persist($paymentDomain);
		$em->flush();

		$independentPaymentVisibilityCalculation =
			$this->getContainer()->get(IndependentPaymentVisibilityCalculation::class);
		/* @var $independentPaymentVisibilityCalculation \SS6\ShopBundle\Model\Payment\IndependentPaymentVisibilityCalculation */

		$this->assertTrue($independentPaymentVisibilityCalculation->isIndependentlyVisible($payment, $domainId));
	}

	public function testIsIndependentlyVisibleEmptyName() {
		$em = $this->getEntityManager();

		$domainId = 1;
		$vat = new Vat(new VatData('vat', 21));
		$payment = new Payment(new PaymentData(['cs' => null], $vat, [], [], false));

		$em->persist($vat);
		$em->persist($payment);
		$em->flush();

		$paymentDomain = new PaymentDomain($payment, $domainId);
		$em->persist($paymentDomain);
		$em->flush();

		$independentPaymentVisibilityCalculation =
			$this->getContainer()->get(IndependentPaymentVisibilityCalculation::class);
		/* @var $independentPaymentVisibilityCalculation \SS6\ShopBundle\Model\Payment\IndependentPaymentVisibilityCalculation */

		$this->assertFalse($independentPaymentVisibilityCalculation->isIndependentlyVisible($payment, $domainId));
	}

	public function testIsIndependentlyVisibleNotOnDomain() {
		$em = $this->getEntityManager();

		$domainId = 1;
		$diffetentDomainId = 2;
		$vat = new Vat(new VatData('vat', 21));
		$payment = new Payment(new PaymentData(['cs' => 'name'], $vat, [], [], false));

		$em->persist($vat);
		$em->persist($payment);
		$em->flush();

		$paymentDomain = new PaymentDomain($payment, $diffetentDomainId);
		$em->persist($paymentDomain);
		$em->flush();

		$independentPaymentVisibilityCalculation =
			$this->getContainer()->get(IndependentPaymentVisibilityCalculation::class);
		/* @var $independentPaymentVisibilityCalculation \SS6\ShopBundle\Model\Payment\IndependentPaymentVisibilityCalculation */

		$this->assertFalse($independentPaymentVisibilityCalculation->isIndependentlyVisible($payment, $domainId));
	}

	public function testIsIndependentlyVisibleHidden() {
		$em = $this->getEntityManager();

		$domainId = 1;
		$vat = new Vat(new VatData('vat', 21));
		$payment = new Payment(new PaymentData(['cs' => 'name'], $vat, [], [], true));

		$em->persist($vat);
		$em->persist($payment);
		$em->flush();

		$paymentDomain = new PaymentDomain($payment, $domainId);
		$em->persist($paymentDomain);
		$em->flush();

		$independentPaymentVisibilityCalculation =
			$this->getContainer()->get(IndependentPaymentVisibilityCalculation::class);
		/* @var $independentPaymentVisibilityCalculation \SS6\ShopBundle\Model\Payment\IndependentPaymentVisibilityCalculation */

		$this->assertFalse($independentPaymentVisibilityCalculation->isIndependentlyVisible($payment, $domainId));
	}

}
