<?php

namespace Shopsys\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\FrameworkBundle\Component\Migration\MultidomainMigrationTrait;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180216091409 extends AbstractMigration
{
    use MultidomainMigrationTrait;

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        foreach ($this->getAllDomainIds() as $domainId) {
            $this->sql(
                'INSERT INTO setting_values (name, domain_id, value, type) 
                VALUES (\'personalDataSiteContent\', :domainId, \'\', \'string\')',
                ['domainId' => $domainId]
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
