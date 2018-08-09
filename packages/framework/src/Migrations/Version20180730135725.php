<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180730135725 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql(
            'CREATE FUNCTION field(integer, integer[])
            RETURNS integer AS
            $$
            SELECT COALESCE(( SELECT i FROM generate_subscripts($2, 1) gs(i) WHERE $2[i] = $1 ), 0)
            $$
            LANGUAGE SQL STABLE'
        );
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
