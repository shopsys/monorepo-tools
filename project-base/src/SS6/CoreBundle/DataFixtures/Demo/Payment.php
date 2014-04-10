<?php

namespace SS6\CoreBundle\DataFixtures\Demo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\CoreBundle\Model\Payment\Entity\Payment as PaymentEntity;

class Payment extends AbstractFixture implements OrderedFixtureInterface {

	/**
	 * @param ObjectManager $manager
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
	 * @param boolean $deleted
	 */
	private function createPayment(
			ObjectManager $manager, $name, $price, array $transportsReferenceName, $description, $hide = false) {
		$transports = new ArrayCollection();
		foreach ($transportsReferenceName as $referenceName) {
			$transports->add($this->getReference($referenceName));
		}
		$payment = new PaymentEntity($name, $price, $transports, $description, $hide);
		$manager->persist($payment);
	}

	/**
	 * @return int
	 */
	public function getOrder() {
		return 15;
	}	
}