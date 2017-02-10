<?php

namespace Shopsys\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20161124154622 extends AbstractMigration
{

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema) {
        $this->sql('ALTER TABLE products_top ADD position INT NOT NULL DEFAULT 0');
        $this->sql('ALTER TABLE products_top ALTER position DROP DEFAULT');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema) {
    }

}
