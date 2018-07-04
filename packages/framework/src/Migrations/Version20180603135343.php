<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180603135343 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $vatsCount = $this->sql('SELECT count(*) FROM vats')->fetchColumn(0);
        if ($vatsCount <= 0) {
            $this->sql('INSERT INTO vats (id, replace_with_id, name, percent) VALUES (1, null, \'Zero rate\', 0)');
            $this->sql('ALTER SEQUENCE vats_id_seq RESTART WITH 2');

            $defaultVatId = $this->sql('SELECT COUNT(*) FROM setting_values WHERE name = \'defaultVatId\' AND domain_id = 0;')->fetchColumn(0);
            if ($defaultVatId <= 0) {
                $this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES (\'defaultVatId\', 0, 1, \'integer\')');
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
