<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20170801093853 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('ALTER TABLE parameter_titles RENAME TO parameters');
        $this->sql('ALTER SEQUENCE parameter_titles_id_seq RENAME TO parameters_id_seq');
        $this->sql('ALTER TABLE parameters RENAME CONSTRAINT parameter_titles_pkey TO parameters_pkey');
        $this->sql('ALTER TABLE parameter_translations RENAME CONSTRAINT parameter_titles_translations_pkey TO parameters_translations_pkey');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
