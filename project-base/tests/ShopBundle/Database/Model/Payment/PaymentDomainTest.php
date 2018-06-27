<?php

namespace Tests\ShopBundle\Database\Model\Payment;

use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Payment\PaymentDataFactory;
use Shopsys\FrameworkBundle\Model\Payment\PaymentFactory;
use Tests\ShopBundle\Test\DatabaseTestCase;

class PaymentDomainTest extends DatabaseTestCase
{
    const FIRST_DOMAIN_ID = 1;
    const SECOND_DOMAIN_ID = 2;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentDataFactory
     */
    private $paymentDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentFactory
     */
    private $paymentFactory;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function setUp()
    {
        parent::setUp();
        $this->paymentDataFactory = $this->getContainer()->get(PaymentDataFactory::class);
        $this->paymentFactory = $this->getContainer()->get(PaymentFactory::class);
        $this->em = $this->getEntityManager();
    }

    public function testCreatePaymentEnabledOnDomain()
    {
        $paymentData = $this->paymentDataFactory->create();

        $paymentData->enabled = [
            self::FIRST_DOMAIN_ID => true,
        ];

        $payment = $this->paymentFactory->create($paymentData);

        $refreshedPayment = $this->getRefreshedPaymentFromDatabase($payment);

        $this->assertTrue($refreshedPayment->isEnabled(self::FIRST_DOMAIN_ID));
    }

    public function testCreatePaymentDisabledOnDomain()
    {
        $paymentData = $this->paymentDataFactory->create();

        $paymentData->enabled[self::FIRST_DOMAIN_ID] = false;

        $payment = $this->paymentFactory->create($paymentData);

        $refreshedPayment = $this->getRefreshedPaymentFromDatabase($payment);

        $this->assertFalse($refreshedPayment->isEnabled(self::FIRST_DOMAIN_ID));
    }

    public function testCreatePaymentWithDifferentVisibilityOnDomains()
    {
        $paymentData = $this->paymentDataFactory->create();

        $paymentData->enabled[self::FIRST_DOMAIN_ID] = true;
        $paymentData->enabled[self::SECOND_DOMAIN_ID] = false;

        $payment = $this->paymentFactory->create($paymentData);

        $refreshedPayment = $this->getRefreshedPaymentFromDatabase($payment);

        $this->assertTrue($refreshedPayment->isEnabled(self::FIRST_DOMAIN_ID));
        $this->assertFalse($refreshedPayment->isEnabled(self::SECOND_DOMAIN_ID));
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment
     */
    private function getRefreshedPaymentFromDatabase(Payment $payment)
    {
        $this->em->persist($payment);
        $this->em->flush();

        $paymentId = $payment->getId();

        $this->em->clear();

        return $this->em->getRepository(Payment::class)->find($paymentId);
    }
}
