<?php

namespace Shopsys\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractNativeFixture;

class DbFunctionsDataFixture extends AbstractNativeFixture
{

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {
        $this->executeNativeQuery('CREATE EXTENSION IF NOT EXISTS unaccent WITH SCHEMA pg_catalog');
        $this->executeNativeQuery('DROP FUNCTION IF EXISTS immutable_unaccent(text)');
        $this->executeNativeQuery('CREATE FUNCTION immutable_unaccent(text)
            RETURNS text AS
            $$
            SELECT unaccent(\'unaccent\', $1)
            $$
            LANGUAGE SQL IMMUTABLE');

        $this->executeNativeQuery('DROP FUNCTION IF EXISTS normalize(text)');
        $this->executeNativeQuery('CREATE FUNCTION normalize(text)
            RETURNS text AS
            $$
            SELECT lower(immutable_unaccent($1))
            $$
            LANGUAGE SQL IMMUTABLE');
    }

}
