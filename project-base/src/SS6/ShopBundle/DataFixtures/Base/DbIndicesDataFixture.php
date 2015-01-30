<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractNativeFixture;

class DbIndicesDataFixture extends AbstractNativeFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$this->executeNativeQuery('CREATE INDEX product_translations_name_normalize_idx
			ON product_translations (NORMALIZE(name))');
		$this->executeNativeQuery('CREATE INDEX product_catnum_normalize_idx
			ON products (NORMALIZE(catnum))');
		$this->executeNativeQuery('CREATE INDEX product_partno_normalize_idx
			ON products (NORMALIZE(partno))');
	}

	public function getDependencies() {
		return [
			DbFunctionsDataFixture::class,
		];
	}

}
