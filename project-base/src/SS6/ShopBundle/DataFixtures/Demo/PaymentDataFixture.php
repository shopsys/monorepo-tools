<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Base\CurrencyDataFixture;
use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;
use SS6\ShopBundle\Model\Payment\PaymentEditData;

class PaymentDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$paymentEditData = new PaymentEditData();
		$paymentEditData->paymentData->name = [
			'cs' => 'Kreditní kartou',
			'en' => 'Credit card',
		];
		$paymentEditData->prices = [
			$this->getReference(CurrencyDataFixture::CURRENCY_CZK)->getId() => 99.95,
			$this->getReference(CurrencyDataFixture::CURRENCY_EUR)->getId() => 2.95,
		];
		$paymentEditData->paymentData->description = [
			'cs' => 'Rychle, levně a spolehlivě!',
			'en' => 'Quick, cheap and reliable!',
		];
		$paymentEditData->paymentData->instructions = [
			'cs' => '<b>Zvolili jste platbu kreditní kartou. Prosím proveďte ji do dvou pracovních dnů.</b>',
			'en' => '<b>You have chosen payment by credit card. Please finish it in two business days.</b>',
		];
		$paymentEditData->paymentData->vat = $this->getReference(VatDataFixture::VAT_ZERO);
		$paymentEditData->paymentData->domains = [1, 2];
		$paymentEditData->paymentData->hidden = false;
		$this->createPayment('payment_card', $paymentEditData, ['transport_personal', 'transport_ppl']);

		$paymentEditData->paymentData->name = [
			'cs' => 'Dobírka',
			'en' => 'Personal collection',
		];
		$paymentEditData->prices = [
			$this->getReference(CurrencyDataFixture::CURRENCY_CZK)->getId() => 49.90,
			$this->getReference(CurrencyDataFixture::CURRENCY_EUR)->getId() => 1.95,
		];
		$paymentEditData->paymentData->description = [];
		$paymentEditData->paymentData->instructions = [];
		$paymentEditData->paymentData->vat = $this->getReference(VatDataFixture::VAT_HIGH);
		$this->createPayment('payment_cod', $paymentEditData, ['transport_cp']);

		$paymentEditData->paymentData->name = [
			'cs' => 'Hotově',
			'en' => 'Cash',
		];
		$paymentEditData->prices = [
			$this->getReference(CurrencyDataFixture::CURRENCY_CZK)->getId() => 0,
			$this->getReference(CurrencyDataFixture::CURRENCY_EUR)->getId() => 0,
		];
		$paymentEditData->paymentData->description = [];
		$paymentEditData->paymentData->vat = $this->getReference(VatDataFixture::VAT_HIGH);
		$this->createPayment('payment_cash', $paymentEditData, ['transport_personal']);

		$manager->flush();
	}

	/**
	 * @param string $referenceName
	 * @param \SS6\ShopBundle\Model\Payment\PaymentEditData $paymentEditData
	 * @param array $transportsReferenceNames
	 */
	private function createPayment(
		$referenceName,
		PaymentEditData $paymentEditData,
		array $transportsReferenceNames
	) {
		$paymentEditFacade = $this->get('ss6.shop.payment.payment_edit_facade');
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */

		$payment = $paymentEditFacade->create($paymentEditData);

		foreach ($transportsReferenceNames as $transportsReferenceName) {
			$payment->addTransport($this->getReference($transportsReferenceName));
		}

		$this->addReference($referenceName, $payment);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return [
			TransportDataFixture::class,
			VatDataFixture::class,
			CurrencyDataFixture::class,
		];
	}

}
