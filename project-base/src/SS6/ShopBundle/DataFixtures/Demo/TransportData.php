<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\Transport\Transport;

class TransportTada extends AbstractFixture implements OrderedFixtureInterface {

	/**
	 * @param ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$this->createTransport($manager, 'transport_cp', 'Česká pošta - balík do ruky', 99.95, '<p>Pouze na vlastní nebezpečí</p>');
		$this->createTransport($manager, 'transport_ppl', 'PPL', 199.95, null);
		$this->createTransport($manager, 'transport_personal', 'Osobní převzetí', 0, '<p>Uvítá Vás milý personál!</p>');
		$manager->flush();
	}
	
	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param string $name
	 * @param string $price
	 * @param string $referenceName
	 * @param string|null $description
	 * @param boolean $hide
	 * @param boolean $deleted
	 */
	private function createTransport(ObjectManager $manager, $referenceName, $name, $price, $description, $hide = false) {
		$transport = new Transport($name, $price, $description, $hide);
		$manager->persist($transport);
		$this->addReference($referenceName, $transport);
	}

	/**
	 * @return int
	 */
	public function getOrder() {
		return 10; // before PaymentData
	}	
}