<?php

namespace Tests\ShopBundle\Database\Model\Payment;

use Shopsys\FrameworkBundle\Model\Payment\IndependentPaymentVisibilityCalculation;
use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Payment\PaymentDataFactory;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Tests\ShopBundle\Test\DatabaseTestCase;

class IndependentPaymentVisibilityCalculationTest extends DatabaseTestCase
{
    const FIRST_DOMAIN_ID = 1;
    const SECOND_DOMAIN_ID = 2;

    public function testIsIndependentlyVisible()
    {
        $em = $this->getEntityManager();
        $vat = $this->getDefaultVat();

        $enabledForDomains = [
            self::FIRST_DOMAIN_ID => true,
            self::SECOND_DOMAIN_ID => true,
        ];
        $payment = $this->getDefaultPayment($vat, $enabledForDomains, false);

        $em->persist($vat);
        $em->persist($payment);
        $em->flush();

        $independentPaymentVisibilityCalculation =
            $this->getContainer()->get(IndependentPaymentVisibilityCalculation::class);
        /* @var $independentPaymentVisibilityCalculation \Shopsys\FrameworkBundle\Model\Payment\IndependentPaymentVisibilityCalculation */

        $this->assertTrue($independentPaymentVisibilityCalculation->isIndependentlyVisible($payment, self::FIRST_DOMAIN_ID));
    }

    public function testIsIndependentlyVisibleEmptyName()
    {
        $em = $this->getEntityManager();
        $vat = $this->getDefaultVat();

        $paymentData = $this->getPaymentDataFactory()->createDefault();
        $paymentData->name = [
            'cs' => null,
            'en' => null,
        ];
        $paymentData->vat = $vat;
        $paymentData->hidden = false;
        $paymentData->enabled = [
            self::FIRST_DOMAIN_ID => true,
            self::SECOND_DOMAIN_ID => false,
        ];

        $payment = new Payment($paymentData);

        $em->persist($vat);
        $em->persist($payment);
        $em->flush();

        $independentPaymentVisibilityCalculation =
            $this->getContainer()->get(IndependentPaymentVisibilityCalculation::class);
        /* @var $independentPaymentVisibilityCalculation \Shopsys\FrameworkBundle\Model\Payment\IndependentPaymentVisibilityCalculation */

        $this->assertFalse($independentPaymentVisibilityCalculation->isIndependentlyVisible($payment, self::FIRST_DOMAIN_ID));
    }

    public function testIsIndependentlyVisibleNotOnDomain()
    {
        $em = $this->getEntityManager();
        $vat = $this->getDefaultVat();

        $enabledForDomains = [
            self::FIRST_DOMAIN_ID => false,
            self::SECOND_DOMAIN_ID => false,
        ];
        $payment = $this->getDefaultPayment($vat, $enabledForDomains, false);

        $em->persist($vat);
        $em->persist($payment);
        $em->flush();

        $independentPaymentVisibilityCalculation =
            $this->getContainer()->get(IndependentPaymentVisibilityCalculation::class);
        /* @var $independentPaymentVisibilityCalculation \Shopsys\FrameworkBundle\Model\Payment\IndependentPaymentVisibilityCalculation */

        $this->assertFalse($independentPaymentVisibilityCalculation->isIndependentlyVisible($payment, self::FIRST_DOMAIN_ID));
    }

    public function testIsIndependentlyVisibleHidden()
    {
        $em = $this->getEntityManager();
        $vat = $this->getDefaultVat();

        $enabledForDomains = [
            self::FIRST_DOMAIN_ID => false,
            self::SECOND_DOMAIN_ID => false,
        ];
        $payment = $this->getDefaultPayment($vat, $enabledForDomains, false);

        $em->persist($vat);
        $em->persist($payment);
        $em->flush();

        $independentPaymentVisibilityCalculation =
            $this->getContainer()->get(IndependentPaymentVisibilityCalculation::class);
        /* @var $independentPaymentVisibilityCalculation \Shopsys\FrameworkBundle\Model\Payment\IndependentPaymentVisibilityCalculation */

        $this->assertFalse($independentPaymentVisibilityCalculation->isIndependentlyVisible($payment, self::FIRST_DOMAIN_ID));
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

        $paymentData = $paymentDataFactory->createDefault();
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
}
