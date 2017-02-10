<?php

namespace Shopsys\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20160422164530 extends AbstractMigration
{

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema) {
        $this->sql('ALTER TABLE products ALTER availability_id DROP NOT NULL;');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema) {

    }

}
