<?php

namespace Shopsys\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20170802141735 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql(
            'CREATE TABLE plugin_data_values (
                plugin_name VARCHAR(255) NOT NULL,
                context VARCHAR(255) NOT NULL,
                key VARCHAR(255) NOT NULL,
                json_value TEXT NOT NULL,
                PRIMARY KEY(plugin_name, context, key)
            )'
        );
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
