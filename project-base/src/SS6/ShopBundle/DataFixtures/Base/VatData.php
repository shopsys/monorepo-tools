<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\Pricing\Vat;

class VatData extends AbstractFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$this->createVat($manager, 'vat_zero', 'Nulová sazba', '0');
		$this->createVat($manager, 'vat_low', 'Nižší sazba', '15');
		$this->createVat($manager, 'vat_high', 'Vyšší sazba', '21');

		$manager->flush();
	}

	private function createVat(ObjectManager $manager, $referenceName, $name, $percent) {
		$vat = new Vat($name, $percent);
		$manager->persist($vat);
		$this->addReference($referenceName, $vat);
	}

}
