<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;

class VatDataFixture extends AbstractFixture {

	const VAT_ZERO = 'vat_zero';
	const VAT_LOW = 'vat_low';
	const VAT_HIGH = 'vat_high';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$this->createVat($manager, self::VAT_ZERO, 'Nulová sazba', '0');
		$this->createVat($manager, self::VAT_LOW, 'Nižší sazba', '15');
		$this->createVat($manager, self::VAT_HIGH, 'Vyšší sazba', '21');

		$manager->flush();
	}

	private function createVat(ObjectManager $manager, $referenceName, $name, $percent) {
		$vat = new Vat($name, $percent);
		$manager->persist($vat);
		$this->addReference($referenceName, $vat);
	}

}
