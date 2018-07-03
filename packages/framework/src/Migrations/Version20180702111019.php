<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180702111019 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $defaultAvailabilityInStockId = $this->sql('SELECT COUNT(*) FROM setting_values WHERE name = \'defaultAvailabilityInStockId\' AND domain_id = 0;')->fetchColumn(0);
        if ($defaultAvailabilityInStockId <= 0) {
            $this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES (\'defaultAvailabilityInStockId\', 0, null, \'integer\')');
        }
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
