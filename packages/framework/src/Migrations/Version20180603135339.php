<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180603135339 extends AbstractMigration
{
    use MultidomainMigrationTrait;

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('ALTER TABLE transport_domains DROP CONSTRAINT "transport_domains_pkey"');
        $this->sql('ALTER TABLE transport_domains ADD id SERIAL NOT NULL');
        $this->sql('ALTER TABLE transport_domains ADD PRIMARY KEY (id)');

        // In previous implementation, records in transport_domains were only created when the transport was enabled on the domain
        // Currently, there should be a record for each transport - domain pair
        $this->sql('ALTER TABLE transport_domains ADD enabled BOOLEAN NOT NULL DEFAULT TRUE');
        $this->sql('ALTER TABLE transport_domains ALTER enabled DROP DEFAULT;');
        $this->sql('CREATE UNIQUE INDEX transport_domain ON transport_domains (transport_id, domain_id)');

        // Because there is a compound unique key for transport and domain we can insert the remaining records
        foreach ($this->getAllDomainIds() as $domainId) {
            $this->sql(
                'INSERT INTO transport_domains (transport_id, domain_id, enabled) 
                    SELECT id, :domainId, FALSE FROM transports
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
