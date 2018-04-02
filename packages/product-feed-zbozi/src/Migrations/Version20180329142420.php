<?php

namespace Shopsys\ProductFeed\ZboziBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180329142420 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('
            CREATE TABLE zbozi_product_domains (
                id SERIAL NOT NULL,
                product_id INT NOT NULL,
                show BOOLEAN DEFAULT NULL,
                cpc NUMERIC(20, 6) DEFAULT NULL,
                cpc_search NUMERIC(20, 6) DEFAULT NULL,
                domain_id INT NOT NULL,
                PRIMARY KEY(id)
            )');
        $this->sql('CREATE INDEX IDX_605AB7A64584665A ON zbozi_product_domains (product_id)');
        $this->sql('
            ALTER TABLE
                zbozi_product_domains
            ADD
                CONSTRAINT FK_605AB7A64584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->sql('ALTER TABLE zbozi_product_domains ALTER show SET NOT NULL');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
