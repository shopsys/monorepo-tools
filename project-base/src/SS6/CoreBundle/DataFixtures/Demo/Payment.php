<?php

namespace SS6\CoreBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\CoreBundle\Model\Payment\Entity\Payment as PaymentEntity;

class Payment extends AbstractFixture implements OrderedFixtureInterface {

	/**
	 * @param ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$this->createPayment($manager, 'Kreditní kartou', 0, '<p>Rychle, levně a spolehlivě!</p>');
		$this->createPayment($manager, 'Dobírka', 49.90, null);
		$this->createPayment($manager, 'Hotově', 0, null);
		$manager->flush();
	}
	
	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param string $name
	 * @param string $price
	 * @param string|null $description
	 * @param boolean $hide
	 * @param boolean $deleted
	 */
	private function createPayment(ObjectManager $manager, $name, $price, $description, $hide = false, $deleted = false) {
		$payment = new PaymentEntity();
		$payment->setName($name);
		$payment->setPrice($price);
		$payment->setDescription($description);
		$payment->setHidden($hide);
		$payment->setDeleted($deleted);
		$manager->persist($payment);
	}

	/**
	 * @return int
	 */
	public function getOrder() {
		return 1;
	}	
}