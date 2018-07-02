<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180603135346 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $pricingGroupsCount = $this->sql('SELECT count(*) FROM pricing_groups')->fetchColumn(0);
        if ($pricingGroupsCount <= 0) {
            $this->sql('INSERT INTO pricing_groups (id, name, domain_id, coefficient) VALUES (1, \'Ordinary customer\', 1, 1)');
            $this->sql('ALTER SEQUENCE pricing_groups_id_seq RESTART WITH 2');

            $defaultPricingGroupId = $this->sql('SELECT COUNT(*) FROM setting_values WHERE name = \'defaultPricingGroupId\' AND domain_id = 1;')->fetchColumn(0);
            if ($defaultPricingGroupId <= 0) {
                $this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES (\'defaultPricingGroupId\', 1, 1, \'integer\')');
            }
        }
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
