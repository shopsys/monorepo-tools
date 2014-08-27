<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportData;

class TransportDataFixture extends AbstractFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		// @codingStandardsIgnoreStart
		$this->createTransport($manager, 'transport_cp', 'Česká pošta - balík do ruky', 99.95, VatDataFixture::VAT_HIGH, 'Pouze na vlastní nebezpečí');
		$this->createTransport($manager, 'transport_ppl', 'PPL', 199.95, VatDataFixture::VAT_HIGH, null);
		$this->createTransport($manager, 'transport_personal', 'Osobní převzetí', 0, VatDataFixture::VAT_ZERO, 'Uvítá Vás milý personál!');
		// @codingStandardsIgnoreStop
		$manager->flush();
	}
	
	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param string $referenceName
	 * @param string $name
	 * @param string $price
	 * @param string|null $description
	 * @param boolean $hide
	 */
	private function createTransport(ObjectManager $manager, $referenceName, $name, $price, $vatReferenceName, $description, $hide = false) {
		$vat = $this->getReference($vatReferenceName);
		$transport = new Transport(new TransportData($name, $price, $vat, $description, $hide));
		$manager->persist($transport);
		$this->addReference($referenceName, $transport);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return array(
			VatDataFixture::class,
		);
	}

}
