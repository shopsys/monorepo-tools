<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20181008000001 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('UPDATE setting_values SET value = 0 WHERE value IS NULL AND type = \'integer\' AND  name = \'' . Setting::DEFAULT_AVAILABILITY_IN_STOCK . '\'');
        $this->sql('UPDATE setting_values SET value = 0 WHERE value IS NULL AND type = \'integer\' AND  name = \'' . Setting::DEFAULT_UNIT . '\'');
        $this->sql('UPDATE setting_values SET type = \'none\' where value IS NULL');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
