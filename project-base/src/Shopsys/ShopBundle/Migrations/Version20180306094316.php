<?php

namespace Shopsys\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180306094316 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('
            CREATE TABLE personal_data_access_request (
                id SERIAL NOT NULL,
                email VARCHAR(255) NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                hash VARCHAR(255) NOT NULL,
                domain_id INT NOT NULL,
                PRIMARY KEY(id)
            )');
        $this->sql('CREATE UNIQUE INDEX UNIQ_C84FBB18D1B862B8 ON personal_data_access_request (hash)');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
