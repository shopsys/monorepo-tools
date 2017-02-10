<?php

namespace Shopsys\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20160108183213 extends AbstractMigration {

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema) {
        $sql = 'CREATE TABLE scripts
            (id SERIAL NOT NULL, name TEXT NOT NULL, code TEXT NOT NULL, PRIMARY KEY(id));';
        $this->sql($sql);
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema) {

    }

}
