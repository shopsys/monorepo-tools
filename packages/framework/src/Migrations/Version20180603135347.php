<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180603135347 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->createMailTemplateIfNotExist('order_status_1', 'true');
        $this->createMailTemplateIfNotExist('order_status_2', 'false');
        $this->createMailTemplateIfNotExist('order_status_3', 'false');
        $this->createMailTemplateIfNotExist('order_status_4', 'false');
        $this->createMailTemplateIfNotExist('registration_confirm', 'true');
        $this->createMailTemplateIfNotExist('reset_password', 'true');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }

    /**
     * @param string $mailTemplateName
     * @param string $sendMail
     */
    private function createMailTemplateIfNotExist($mailTemplateName, $sendMail)
    {
        $mailTemplateCount = $this->sql('SELECT count(*) FROM mail_templates WHERE name = :mailTemplateName', [
            'mailTemplateName' => $mailTemplateName,
        ])->fetchColumn(0);
        if ($mailTemplateCount <= 0) {
            $this->sql('INSERT INTO mail_templates (name, domain_id, send_mail) VALUES (:mailTemplateName, 1, :sendMail)', [
                'mailTemplateName' => $mailTemplateName,
                'sendMail' => $sendMail,
            ]);
        }
    }
}
