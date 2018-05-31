<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180531141640 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('ALTER TABLE category_domains DROP CONSTRAINT "category_domains_pkey"');
        $this->sql('ALTER TABLE category_domains ADD id SERIAL NOT NULL');
        $this->sql('ALTER TABLE category_domains ADD PRIMARY KEY (id)');
        $this->sql('CREATE UNIQUE INDEX category_domain ON category_domains (category_id, domain_id)');

        $this->sql('ALTER TABLE category_domains RENAME COLUMN hidden TO enabled');
        $this->sql('UPDATE category_domains SET enabled = NOT enabled');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
