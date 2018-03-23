<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractNativeFixture;

class DbFunctionsDataFixture extends AbstractNativeFixture
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->executeNativeQuery('DROP FUNCTION IF EXISTS immutable_unaccent(text)');
        $this->executeNativeQuery('CREATE FUNCTION immutable_unaccent(text)
            RETURNS text AS
            $$
            SELECT pg_catalog.unaccent(\'pg_catalog.unaccent\', $1)
            $$
            LANGUAGE SQL IMMUTABLE');

        $this->executeNativeQuery('DROP FUNCTION IF EXISTS normalize(text)');
        $this->executeNativeQuery('CREATE FUNCTION normalize(text)
            RETURNS text AS
            $$
            SELECT pg_catalog.lower(public.immutable_unaccent($1))
            $$
            LANGUAGE SQL IMMUTABLE');
    }
}
