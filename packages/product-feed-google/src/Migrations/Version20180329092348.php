<?php

namespace Shopsys\ProductFeed\GoogleBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180329092348 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('
            CREATE TABLE google_product_domains (
                id SERIAL NOT NULL,
                product_id INT NOT NULL,
                show BOOLEAN DEFAULT NULL,
                domain_id INT NOT NULL,
                PRIMARY KEY(id)
            )');
        $this->sql('CREATE INDEX IDX_628F4AA44584665A ON google_product_domains (product_id)');
        $this->sql('
            ALTER TABLE
                google_product_domains
            ADD
                CONSTRAINT FK_628F4AA44584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->sql('ALTER TABLE google_product_domains ALTER show SET NOT NULL');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
