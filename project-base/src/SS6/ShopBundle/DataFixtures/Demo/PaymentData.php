<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\Payment\Payment;

class PaymentData extends AbstractFixture implements OrderedFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$this->createPayment($manager, 'Kreditní kartou', 0, array('transport_personal', 'transport_ppl'), 
			'<p>Rychle, levně a spolehlivě!</p>');
		$this->createPayment($manager, 'Dobírka', 49.90, array('transport_cp'), null);
		$this->createPayment($manager, 'Hotově', 0, array('transport_personal'), null);
		$manager->flush();
	}
	
	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param string $name
	 * @param string $price
	 * @param array $transportsReferenceName
	 * @param string|null $description
	 * @param boolean $hide
	 */
	private function createPayment(
			ObjectManager $manager, $name, $price, array $transportsReferenceName, $description, $hide = false) {
		$payment = new Payment($name, $price, $description, $hide);
		foreach ($transportsReferenceName as $referenceName) {
			$payment->addTransport($this->getReference($referenceName));
		}
		$manager->persist($payment);
	}

	/**
	 * @return int
	 */
	public function getOrder() {
		return 15; // after TransportData
	}	
}
