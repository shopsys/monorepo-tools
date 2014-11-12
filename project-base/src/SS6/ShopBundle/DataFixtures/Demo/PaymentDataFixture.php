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
		$paymentData->setNames(array(
			'cs' => 'Kreditní kartou',
			'en' => 'Credit card',
		));
		$paymentData->setPrice(99.95);
		$paymentData->setDescriptions(array(
			'cs' => 'Rychle, levně a spolehlivě!',
			'en' => 'Quick, cheap and reliable!',
		));
		$paymentData->setVat($this->getReference(VatDataFixture::VAT_ZERO));
		$paymentData->setDomains(array(1, 2));
		$paymentData->setHidden(false);
		$this->createPayment('payment_card', $paymentData, array('transport_personal', 'transport_ppl'));

		$paymentData->setNames(array(
			'cs' => 'Dobírka',
			'en' => 'Personal collection',
		));
		$paymentData->setPrice(49.90);
		$paymentData->setDescriptions(array());
		$paymentData->setVat($this->getReference(VatDataFixture::VAT_HIGH));
		$this->createPayment('payment_cod', $paymentData, array('transport_cp'));

		$paymentData->setNames(array(
			'cs' => 'Hotově',
			'en' => 'Cash',
		));
		$paymentData->setPrice(0);
		$paymentData->setDescriptions(array());
		$paymentData->setVat($this->getReference(VatDataFixture::VAT_HIGH));
		$this->createPayment('payment_cash', $paymentData, array('transport_personal'));

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
