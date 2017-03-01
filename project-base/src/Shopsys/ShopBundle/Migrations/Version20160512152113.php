<?php

namespace Shopsys\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;
use Shopsys\ShopBundle\Component\Migration\MultidomainMigrationTrait;

class Version20160512152113 extends AbstractMigration
{
    use MultidomainMigrationTrait;

    const COUNTRIES_SEQUENCE_NAME = 'countries_id_seq';

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function up(Schema $schema)
    {
        $this->sql(
            'CREATE TABLE countries (
                id SERIAL NOT NULL,
                name VARCHAR(255) NOT NULL,
                domain_id INT NOT NULL,
                PRIMARY KEY(id))'
        );

        $this->sql('ALTER TABLE billing_addresses ADD COLUMN country_id INT DEFAULT NULL');
        $this->sql(
            'ALTER TABLE billing_addresses ADD CONSTRAINT FK_DBD91748F92F3E70 FOREIGN KEY (country_id)
            REFERENCES countries (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->sql('CREATE INDEX IDX_DBD91748F92F3E70 ON billing_addresses (country_id)');
        $this->sql('ALTER TABLE delivery_addresses ADD country_id INT DEFAULT NULL');
        $this->sql(
            'ALTER TABLE delivery_addresses ADD CONSTRAINT FK_2BAF3984E76AA954 FOREIGN KEY (country_id)
            REFERENCES countries (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->sql('CREATE INDEX IDX_2BAF3984F92F3E70 ON delivery_addresses (country_id)');

        $this->sql('ALTER TABLE orders ADD country_id INT');
        $this->sql(
            'ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEF92F3E70 FOREIGN KEY (country_id)
            REFERENCES countries (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->sql('CREATE INDEX IDX_E52FFDEEF92F3E70 ON orders (country_id)');
        foreach ($this->getAllDomainIds() as $domainId) {
            $countOfOrdersOnDomain = $this->sql(
                'SELECT COUNT(*) FROM orders WHERE domain_id = :domainId;',
                ['domainId' => $domainId]
            )->fetchColumn(0);

            if ($countOfOrdersOnDomain > 0) {
                $this->sql(
                    'INSERT INTO countries (name, domain_id) VALUES (:countryName, :domainId)',
                    [
                        'countryName' => '-',
                        'domainId' => $domainId,
                    ]
                );
                $countryId = $this->connection->lastInsertId(self::COUNTRIES_SEQUENCE_NAME);
                $this->sql(
                    'UPDATE orders SET country_id = :countryId WHERE domain_id = :domainId',
                    [
                        'countryId' => $countryId,
                        'domainId' => $domainId,
                    ]
                );
            }
        }
        $this->sql('ALTER TABLE orders ALTER country_id SET NOT NULL');

        $this->sql('ALTER TABLE orders ADD delivery_country_id INT DEFAULT NULL');
        $this->sql(
            'ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEE76AA954 FOREIGN KEY (delivery_country_id)
            REFERENCES countries (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->sql('CREATE INDEX IDX_E52FFDEEE76AA954 ON orders (delivery_country_id)');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
