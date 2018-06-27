<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180409055551 extends AbstractMigration
{
    use MultidomainMigrationTrait;

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('UPDATE setting_values SET name = \'personalDataDisplaySiteContent\' WHERE name = \'personalDataSiteContent\'');

        foreach ($this->getAllDomainIds() as $domainId) {
            $this->sql(
                'INSERT INTO mail_templates (name, domain_id, bcc_email, subject, body, send_mail) VALUES
                (\'personal_data_export\', :domainId, null, null, null, false);',
                ['domainId' => $domainId]
            );

            $this->sql(
                'INSERT INTO setting_values (name, domain_id, value, type) 
                VALUES (\'personalDataExportSiteContent\', :domainId, \'\', \'string\')',
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
