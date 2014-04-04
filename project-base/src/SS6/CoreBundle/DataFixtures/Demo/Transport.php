<?php

namespace SS6\CoreBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\CoreBundle\Model\Transport\Entity\Transport as TransportEntity;

class Transport extends AbstractFixture implements OrderedFixtureInterface {

	/**
	 * @param ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$this->createTransport($manager, 'Česká pošta - balík do ruky', 99.95, '<p>Pouze na vlastní nebezpečí</p>');
		$this->createTransport($manager, 'PPL', 199.95, null);
		$this->createTransport($manager, 'Osobní převzetí', 0, '<p>Uvítá Vás milý personál!</p>');
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
	private function createTransport(ObjectManager $manager, $name, $price, $description, $hide = false, $deleted = false) {
		$transport = new TransportEntity();
		$transport->setName($name);
		$transport->setPrice($price);
		$transport->setDescription($description);
		$transport->setHide($hide);
		$transport->setDeleted($deleted);
		$manager->persist($transport);
	}

	/**
	 * @return int
	 */
	public function getOrder() {
		return 1;
	}	
}