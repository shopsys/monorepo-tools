<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractNativeFixture;

class DbCollationsDataFixture extends AbstractNativeFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$this->executeNativeQuery('CREATE COLLATION "cs_CZ" (LOCALE="Czech")');
		$this->executeNativeQuery('CREATE COLLATION "de_DE" (LOCALE="German")');
		$this->executeNativeQuery('CREATE COLLATION "en_US" (LOCALE="English_US")');
		$this->executeNativeQuery('CREATE COLLATION "hu_HU" (LOCALE="Hungarian")');
		$this->executeNativeQuery('CREATE COLLATION "pl_PL" (LOCALE="Polish")');
		$this->executeNativeQuery('CREATE COLLATION "sk_SK" (LOCALE="Slovak")');
	}

}
