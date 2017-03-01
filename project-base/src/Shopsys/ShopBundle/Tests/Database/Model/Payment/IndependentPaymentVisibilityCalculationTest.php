<?php

namespace Shopsys\ShopBundle\Tests\Database\Model\Payment;

use Shopsys\ShopBundle\Model\Payment\IndependentPaymentVisibilityCalculation;
use Shopsys\ShopBundle\Model\Payment\Payment;
use Shopsys\ShopBundle\Model\Payment\PaymentData;
use Shopsys\ShopBundle\Model\Payment\PaymentDomain;
use Shopsys\ShopBundle\Model\Pricing\Vat\Vat;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatData;
use Shopsys\ShopBundle\Tests\Test\DatabaseTestCase;

class IndependentPaymentVisibilityCalculationTest extends DatabaseTestCase
{
    public function testIsIndependentlyVisible()
    {
        $em = $this->getEntityManager();

        $domainId = 1;
        $vat = new Vat(new VatData('vat', 21));
        $payment = new Payment(new PaymentData(['cs' => 'name', 'en' => 'name'], $vat, [], [], false));

        $em->persist($vat);
        $em->persist($payment);
        $em->flush();

        $paymentDomain = new PaymentDomain($payment, $domainId);
        $em->persist($paymentDomain);
        $em->flush();

        $independentPaymentVisibilityCalculation =
            $this->getContainer()->get(IndependentPaymentVisibilityCalculation::class);
        /* @var $independentPaymentVisibilityCalculation \Shopsys\ShopBundle\Model\Payment\IndependentPaymentVisibilityCalculation */

        $this->assertTrue($independentPaymentVisibilityCalculation->isIndependentlyVisible($payment, $domainId));
    }

    public function testIsIndependentlyVisibleEmptyName()
    {
        $em = $this->getEntityManager();

        $domainId = 1;
        $vat = new Vat(new VatData('vat', 21));
        $payment = new Payment(new PaymentData(['cs' => null, 'en' => null], $vat, [], [], false));

        $em->persist($vat);
        $em->persist($payment);
        $em->flush();

        $paymentDomain = new PaymentDomain($payment, $domainId);
        $em->persist($paymentDomain);
        $em->flush();

        $independentPaymentVisibilityCalculation =
            $this->getContainer()->get(IndependentPaymentVisibilityCalculation::class);
        /* @var $independentPaymentVisibilityCalculation \Shopsys\ShopBundle\Model\Payment\IndependentPaymentVisibilityCalculation */

        $this->assertFalse($independentPaymentVisibilityCalculation->isIndependentlyVisible($payment, $domainId));
    }

    public function testIsIndependentlyVisibleNotOnDomain()
    {
        $em = $this->getEntityManager();

        $domainId = 1;
        $diffetentDomainId = 2;
        $vat = new Vat(new VatData('vat', 21));
        $payment = new Payment(new PaymentData(['cs' => 'name', 'en' => 'name'], $vat, [], [], false));

        $em->persist($vat);
        $em->persist($payment);
        $em->flush();

        $paymentDomain = new PaymentDomain($payment, $diffetentDomainId);
        $em->persist($paymentDomain);
        $em->flush();

        $independentPaymentVisibilityCalculation =
            $this->getContainer()->get(IndependentPaymentVisibilityCalculation::class);
        /* @var $independentPaymentVisibilityCalculation \Shopsys\ShopBundle\Model\Payment\IndependentPaymentVisibilityCalculation */

        $this->assertFalse($independentPaymentVisibilityCalculation->isIndependentlyVisible($payment, $domainId));
    }

    public function testIsIndependentlyVisibleHidden()
    {
        $em = $this->getEntityManager();

        $domainId = 1;
        $vat = new Vat(new VatData('vat', 21));
        $payment = new Payment(new PaymentData(['cs' => 'name', 'en' => 'name'], $vat, [], [], true));

        $em->persist($vat);
        $em->persist($payment);
        $em->flush();

        $paymentDomain = new PaymentDomain($payment, $domainId);
        $em->persist($paymentDomain);
        $em->flush();

        $independentPaymentVisibilityCalculation =
            $this->getContainer()->get(IndependentPaymentVisibilityCalculation::class);
        /* @var $independentPaymentVisibilityCalculation \Shopsys\ShopBundle\Model\Payment\IndependentPaymentVisibilityCalculation */

        $this->assertFalse($independentPaymentVisibilityCalculation->isIndependentlyVisible($payment, $domainId));
    }
}
