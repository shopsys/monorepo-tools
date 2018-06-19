<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180530113304 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('ALTER TABLE brand_domains DROP CONSTRAINT "brand_domains_pkey"');
        $this->sql('ALTER TABLE brand_domains ADD id SERIAL NOT NULL');
        $this->sql('ALTER TABLE brand_domains ADD PRIMARY KEY (id)');
        $this->sql('CREATE UNIQUE INDEX brand_domain ON brand_domains (brand_id, domain_id)');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
