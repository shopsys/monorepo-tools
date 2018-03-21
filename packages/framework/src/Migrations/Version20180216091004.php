<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\FrameworkBundle\Component\Migration\MultidomainMigrationTrait;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180216091004 extends AbstractMigration
{
    use MultidomainMigrationTrait;

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('
            ALTER TABLE newsletter_subscribers
            DROP CONSTRAINT "newsletter_subscribers_pkey",
            ADD id SERIAL NOT NULL,
            ADD domain_id INT NULL,
            ADD PRIMARY KEY (id)
        ');

        $newsletterSubscribers = $this->sql('SELECT email, created_at FROM newsletter_subscribers')->fetchAll();
        foreach ($newsletterSubscribers as $newsletterSubscriber) {
            foreach ($this->getAllDomainIds() as $domainId) {
                $this->sql(
                    'INSERT INTO newsletter_subscribers(email, created_at, domain_id) 
                      VALUES (:email, :created_at, :domainId)',
                    [
                        'email' => $newsletterSubscriber['email'],
                        'created_at' => $newsletterSubscriber['created_at'],
                        'domainId' => $domainId,
                    ]
                );
            }
        }
        $this->sql('DELETE FROM newsletter_subscribers WHERE domain_id IS NULL');
        $this->sql('ALTER TABLE newsletter_subscribers ALTER COLUMN domain_id SET NOT NULL');
        $this->sql('CREATE UNIQUE INDEX newsletter_subscribers_uni ON newsletter_subscribers (email, domain_id)');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
