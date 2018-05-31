<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180530131622 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('ALTER TABLE product_domains DROP CONSTRAINT "product_domains_pkey"');
        $this->sql('ALTER TABLE product_domains ADD id SERIAL NOT NULL');
        $this->sql('ALTER TABLE product_domains ADD PRIMARY KEY (id)');
        $this->sql('CREATE UNIQUE INDEX product_domain ON product_domains (product_id, domain_id)');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
