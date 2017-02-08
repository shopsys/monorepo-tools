<?php

namespace SS6\ShopBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Base\CurrencyDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\PaymentDataFixture as DemoPaymentDataFixture;
use SS6\ShopBundle\Model\Payment\PaymentEditDataFactory;
use SS6\ShopBundle\Model\Payment\PaymentEditFacade;

class PaymentDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$paymentEditDataFactory = $this->get(PaymentEditDataFactory::class);
		/* @var $paymentEditDataFactory \SS6\ShopBundle\Model\Payment\PaymentEditDataFactory */
		$paymentEditFacade = $this->get(PaymentEditFacade::class);
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */

		$currencyEur = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);
		/* @var $currencyEur \SS6\ShopBundle\Model\Pricing\Currency\Currency */

		$payment = $this->getReference(DemoPaymentDataFixture::PAYMENT_CARD);
		$paymentEditData = $paymentEditDataFactory->createFromPayment($payment);
		$paymentEditData->paymentData->name['en'] = 'Credit card';
		$paymentEditData->paymentData->description['en'] = 'Quick, cheap and reliable!';
		$paymentEditData->paymentData->instructions['en'] =
			'<b>You have chosen payment by credit card. Please finish it in two business days.</b>';
		$paymentEditData->prices[$currencyEur->getId()] = 2.95;
		$paymentEditFacade->edit($payment, $paymentEditData);

		$payment = $this->getReference(DemoPaymentDataFixture::PAYMENT_CASH_ON_DELIVERY);
		$paymentEditData = $paymentEditDataFactory->createFromPayment($payment);
		$paymentEditData->paymentData->name['en'] = 'Personal collection';
		$paymentEditData->prices[$currencyEur->getId()] = 1.95;
		$paymentEditFacade->edit($payment, $paymentEditData);

		$payment = $this->getReference(DemoPaymentDataFixture::PAYMENT_CASH);
		$paymentEditData = $paymentEditDataFactory->createFromPayment($payment);
		$paymentEditData->paymentData->name['en'] = 'Cash';
		$paymentEditData->prices[$currencyEur->getId()] = 0;
		$paymentEditFacade->edit($payment, $paymentEditData);
	}

}
