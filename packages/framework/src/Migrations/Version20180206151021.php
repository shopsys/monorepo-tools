<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180206151021 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('
            ALTER TABLE
                newsletter_subscribers
            ADD
                created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'1970-01-01 00:00:00\' NOT NULL');
        $this->sql('COMMENT ON COLUMN newsletter_subscribers.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
