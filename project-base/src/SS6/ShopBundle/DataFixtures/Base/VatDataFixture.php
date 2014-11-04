<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;

class VatDataFixture extends AbstractReferenceFixture {

	const VAT_ZERO = 'vat_zero';
	const VAT_LOW = 'vat_low';
	const VAT_HIGH = 'vat_high';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {

		$vatData = new VatData();

		$vatData->setName('Nulová sazba');
		$vatData->setPercent('0');
		$this->createVat($manager, self::VAT_ZERO, $vatData);

		$vatData->setName('Nižší sazba');
		$vatData->setPercent('15');
		$this->createVat($manager, self::VAT_LOW, $vatData);

		$vatData->setName('Vyšší sazba');
		$vatData->setPercent('21');
		$this->createVat($manager, self::VAT_HIGH, $vatData);

		$manager->flush();
	}

	private function createVat(ObjectManager $manager, $referenceName, VatData $vatData) {
		$vat = new Vat($vatData);
		$manager->persist($vat);
		$this->addReference($referenceName, $vat);
	}

}
