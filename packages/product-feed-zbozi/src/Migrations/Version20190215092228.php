<?php

namespace Shopsys\ProductFeed\ZboziBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20190215092228 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('COMMENT ON COLUMN zbozi_product_domains.cpc IS \'(DC2Type:money)\'');
        $this->sql('COMMENT ON COLUMN zbozi_product_domains.cpc_search IS \'(DC2Type:money)\'');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
