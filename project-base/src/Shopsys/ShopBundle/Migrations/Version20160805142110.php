<?php

namespace Shopsys\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20160805142110 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema) {
        $this->sql('ALTER TABLE product_domains ADD show_in_zbozi_feed BOOLEAN NOT NULL DEFAULT TRUE');
        $this->sql('ALTER TABLE product_domains ALTER show_in_zbozi_feed DROP DEFAULT');

        $this->sql('ALTER TABLE product_domains ADD zbozi_cpc NUMERIC(16, 2) DEFAULT NULL');
        $this->sql('ALTER TABLE product_domains ADD zbozi_cpc_search NUMERIC(16, 2) DEFAULT NULL');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema) {
    }
}
