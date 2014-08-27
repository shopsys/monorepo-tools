<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentData;

class PaymentDataFixture extends AbstractFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		// @codingStandardsIgnoreStart
		$this->createPayment($manager, 'payment_card', 'Kreditní kartou', 0, VatDataFixture::VAT_ZERO, array('transport_personal', 'transport_ppl'), 'Rychle, levně a spolehlivě!');
		$this->createPayment($manager, 'payment_cod', 'Dobírka', 49.90, VatDataFixture::VAT_HIGH, array('transport_cp'), null);
		$this->createPayment($manager, 'payment_cash', 'Hotově', 0, VatDataFixture::VAT_HIGH, array('transport_personal'), null);
		// @codingStandardsIgnoreStop
		$manager->flush();
	}
	
	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param string $name
	 * @param string $price
	 * @param array $transportsReferenceNames
	 * @param string|null $description
	 * @param boolean $hide
	 */
	private function createPayment(
		ObjectManager $manager,
		$referenceName,
		$name,
		$price,
		$vatReferenceName,
		array $transportsReferenceNames,
		$description,
		$hide = false
	) {
		$vat = $this->getReference($vatReferenceName);

		$payment = new Payment(new PaymentData($name, $price, $vat, $description, $hide));

		foreach ($transportsReferenceNames as $transportsReferenceName) {
			$payment->addTransport($this->getReference($transportsReferenceName));
		}
		
		$manager->persist($payment);
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
