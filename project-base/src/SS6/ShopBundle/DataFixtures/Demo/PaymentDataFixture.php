<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;
use SS6\ShopBundle\Model\Payment\PaymentData;

class PaymentDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$paymentData = new PaymentData();
		$paymentData->name = [
			'cs' => 'Kreditní kartou',
			'en' => 'Credit card',
		];
		$paymentData->price = 99.95;
		$paymentData->description = [
			'cs' => 'Rychle, levně a spolehlivě!',
			'en' => 'Quick, cheap and reliable!',
		];
		$paymentData->instructions = [
			'cs' => '<b>Zvolili jste platbu kreditní kartou. Prosím proveďte ji do dvou pracovních dnů.</b>',
			'en' => '<b>You have chosen payment by credit card. Please finish it in two business days.</b>',
		];
		$paymentData->vat = $this->getReference(VatDataFixture::VAT_ZERO);
		$paymentData->domains = [1, 2];
		$paymentData->hidden = false;
		$this->createPayment('payment_card', $paymentData, ['transport_personal', 'transport_ppl']);

		$paymentData->name = [
			'cs' => 'Dobírka',
			'en' => 'Personal collection',
		];
		$paymentData->price = 49.90;
		$paymentData->description = [];
		$paymentData->vat = $this->getReference(VatDataFixture::VAT_HIGH);
		$this->createPayment('payment_cod', $paymentData, ['transport_cp']);

		$paymentData->name = [
			'cs' => 'Hotově',
			'en' => 'Cash',
		];
		$paymentData->price = 0;
		$paymentData->description = [];
		$paymentData->vat = $this->getReference(VatDataFixture::VAT_HIGH);
		$this->createPayment('payment_cash', $paymentData, ['transport_personal']);

		$manager->flush();
	}

	/**
	 * @param string $referenceName
	 * @param \SS6\ShopBundle\Model\Payment\PaymentData $paymentData
	 * @param array $transportsReferenceNames
	 */
	private function createPayment(
		$referenceName,
		PaymentData $paymentData,
		array $transportsReferenceNames
	) {
		$paymentEditFacade = $this->get('ss6.shop.payment.payment_edit_facade');
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */

		$payment = $paymentEditFacade->create($paymentData);

		foreach ($transportsReferenceNames as $transportsReferenceName) {
			$payment->addTransport($this->getReference($transportsReferenceName));
		}

		$this->addReference($referenceName, $payment);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return array(
			TransportDataFixture::class,
			VatDataFixture::class,
		);
	}

}
