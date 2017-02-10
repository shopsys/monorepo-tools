<?php

namespace Shopsys\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;
use Shopsys\ShopBundle\Component\Setting\Setting;

class Version20160531080553 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES
            (\'' . Setting::FEED_DOMAIN_ID_TO_CONTINUE . '\', 0, NULL, \'string\'),
            (\'' . Setting::FEED_ITEM_ID_TO_CONTINUE . '\', 0, NULL, \'string\'),
            (\'' . Setting::FEED_NAME_TO_CONTINUE . '\', 0, NULL, \'string\')
        ');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
