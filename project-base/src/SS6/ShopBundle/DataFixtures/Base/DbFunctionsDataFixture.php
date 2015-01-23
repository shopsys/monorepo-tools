<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\ResultSetMapping;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;

class DbFunctionsDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$em = $this->get('doctrine.orm.entity_manager');
		/* @var $em \Doctrine\ORM\EntityManager */

		// immutable version of unaccent() to support indexing
		$em->createNativeQuery('CREATE FUNCTION immutable_unaccent(text)
			RETURNS text AS
			$$
			SELECT unaccent(\'unaccent\', $1)
			$$
			LANGUAGE SQL IMMUTABLE', new ResultSetMapping())->execute();

		$em->createNativeQuery('CREATE FUNCTION normalize(text)
			RETURNS text AS
			$$
			SELECT lower(immutable_unaccent($1))
			$$
			LANGUAGE SQL IMMUTABLE', new ResultSetMapping())->execute();
	}

}
