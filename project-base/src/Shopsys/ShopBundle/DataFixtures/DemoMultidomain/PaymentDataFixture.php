<?php

namespace Shopsys\ShopBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\DataFixtures\Base\CurrencyDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\PaymentDataFixture as DemoPaymentDataFixture;
use Shopsys\ShopBundle\Model\Payment\PaymentEditDataFactory;
use Shopsys\ShopBundle\Model\Payment\PaymentFacade;

class PaymentDataFixture extends AbstractReferenceFixture
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $paymentEditDataFactory = $this->get(PaymentEditDataFactory::class);
        /* @var $paymentEditDataFactory \Shopsys\ShopBundle\Model\Payment\PaymentEditDataFactory */
        $paymentFacade = $this->get(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\ShopBundle\Model\Payment\PaymentFacade */

        $currencyEur = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);
        /* @var $currencyEur \Shopsys\ShopBundle\Model\Pricing\Currency\Currency */

        $payment = $this->getReference(DemoPaymentDataFixture::PAYMENT_CARD);
        $paymentEditData = $paymentEditDataFactory->createFromPayment($payment);
        $paymentEditData->paymentData->name['en'] = 'Credit card';
        $paymentEditData->paymentData->description['en'] = 'Quick, cheap and reliable!';
        $paymentEditData->paymentData->instructions['en'] =
            '<b>You have chosen payment by credit card. Please finish it in two business days.</b>';
        $paymentEditData->prices[$currencyEur->getId()] = 2.95;
        $paymentFacade->edit($payment, $paymentEditData);

        $payment = $this->getReference(DemoPaymentDataFixture::PAYMENT_CASH_ON_DELIVERY);
        $paymentEditData = $paymentEditDataFactory->createFromPayment($payment);
        $paymentEditData->paymentData->name['en'] = 'Personal collection';
        $paymentEditData->prices[$currencyEur->getId()] = 1.95;
        $paymentFacade->edit($payment, $paymentEditData);

        $payment = $this->getReference(DemoPaymentDataFixture::PAYMENT_CASH);
        $paymentEditData = $paymentEditDataFactory->createFromPayment($payment);
        $paymentEditData->paymentData->name['en'] = 'Cash';
        $paymentEditData->prices[$currencyEur->getId()] = 0;
        $paymentFacade->edit($payment, $paymentEditData);
    }
}
