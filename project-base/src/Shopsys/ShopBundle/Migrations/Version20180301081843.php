<?php

namespace Shopsys\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\FrameworkBundle\Component\Migration\MultidomainMigrationTrait;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180301081843 extends AbstractMigration
{
    use MultidomainMigrationTrait;

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        foreach ($this->getAllDomainIds() as $domainId) {
            $this->sql(
                'INSERT INTO mail_templates (name, domain_id, bcc_email, subject, body, send_mail) VALUES
                (\'personal_data_access\', :domainId, null, null, null, false);',
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
