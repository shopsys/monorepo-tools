<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\FrameworkBundle\Component\Migration\MultidomainMigrationTrait;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20170530101845 extends AbstractMigration
{
    use MultidomainMigrationTrait;

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $demoSettingValues = [
            'shopInfoPhoneNumber' => '+420123456789',
            'shopInfoEmail' => 'no-reply@shopsys.com',
            'shopInfoPhoneHours' => '(po-pá, 10:00 - 16:00)',
        ];
        foreach ($demoSettingValues as $name => $demoValue) {
            // Migration Version20161207144725 added demo setting values that should be set in data fixtures.
            // Therefore we clear it because default values should be empty.
            $this->sql(
                'UPDATE setting_values SET value = NULL WHERE name = :name AND value = :demoValue',
                [
                    'name' => $name,
                    'demoValue' => $demoValue,
                ]
            );
        }
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
