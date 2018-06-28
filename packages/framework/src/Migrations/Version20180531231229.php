<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180531231229 extends AbstractMigration
{
    use MultidomainMigrationTrait;

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('ALTER TABLE payment_domains DROP CONSTRAINT "payment_domains_pkey"');
        $this->sql('ALTER TABLE payment_domains ADD id SERIAL NOT NULL');
        $this->sql('ALTER TABLE payment_domains ADD PRIMARY KEY (id)');

        // In previous implementation, records in payment_domains were only created when the payment was enabled on the domain
        // Currently, there should be a record for each payment - domain pair
        $this->sql('ALTER TABLE payment_domains ADD enabled BOOLEAN NOT NULL DEFAULT TRUE');
        $this->sql('ALTER TABLE payment_domains ALTER enabled DROP DEFAULT;');
        $this->sql('CREATE UNIQUE INDEX payment_domain ON payment_domains (payment_id, domain_id)');

        // Because there is a compound unique key for payment and domain we can insert the remaining records
        foreach ($this->getAllDomainIds() as $domainId) {
            $this->sql(
                'INSERT INTO payment_domains (payment_id, domain_id, enabled)
                    SELECT id, :domainId, FALSE FROM payments
                    ON CONFLICT DO NOTHING',
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
