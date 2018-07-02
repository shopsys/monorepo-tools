<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180702111016 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $enabledModulesCount = $this->sql('SELECT count(*) FROM enabled_modules')->fetchColumn(0);
        if ($enabledModulesCount > 0) {
            return;
        }

        $this->sql(
            'INSERT INTO "enabled_modules" ("name") VALUES '
            . '(\'productFilterCounts\'),'
            . '(\'productStockCalculations\')'
        );
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
