<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\Payment\Payment;

class PaymentData extends AbstractFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$this->createPayment($manager, 'payment_card', 'Kreditní kartou', 0,
			array('transport_personal', 'transport_ppl'), 'Rychle, levně a spolehlivě!');
		$this->createPayment($manager, 'payment_cod', 'Dobírka', 49.90, array('transport_cp'), null);
		$this->createPayment($manager, 'payment_cash', 'Hotově', 0, array('transport_personal'), null);
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
	private function createPayment(ObjectManager $manager, $referenceName, $name, $price,
			array $transportsReferenceNames, $description, $hide = false) {
		$payment = new Payment($name, $price, $description, $hide);
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
			TransportData::class,
		);
	}

}
