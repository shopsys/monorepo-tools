<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;

class VatDataFixture extends AbstractReferenceFixture {

	const VAT_ZERO = 'vat_zero';
	const VAT_SECOND_LOW = 'vat_second_low';
	const VAT_LOW = 'vat_low';
	const VAT_HIGH = 'vat_high';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {

		$vatData = new VatData();

		$vatData->name = 'Nulová sazba';
		$vatData->percent = '0';
		$this->createVat($manager, self::VAT_ZERO, $vatData);

		$vatData->name = 'Druhá nižší sazba';
		$vatData->percent = '10';
		$this->createVat($manager, self::VAT_SECOND_LOW, $vatData);

		$vatData->name = 'Nižší sazba';
		$vatData->percent = '15';
		$this->createVat($manager, self::VAT_LOW, $vatData);

		$vatData->name = 'Vyšší sazba';
		$vatData->percent = '21';
		$this->createVat($manager, self::VAT_HIGH, $vatData);
	}

	private function createVat(ObjectManager $manager, $referenceName, VatData $vatData) {
		$vat = new Vat($vatData);
		$manager->persist($vat);
		$manager->flush($vat);
		$this->addReference($referenceName, $vat);
	}

}
