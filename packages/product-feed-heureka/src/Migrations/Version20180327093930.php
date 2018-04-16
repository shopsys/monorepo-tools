<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180327093930 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('
            CREATE TABLE heureka_category (
                id INT NOT NULL,
                name VARCHAR(100) DEFAULT NULL,
                full_name VARCHAR(255) DEFAULT NULL,
                PRIMARY KEY(id)
            )');
        $this->sql('
            CREATE TABLE heureka_category_categories (
                heureka_category_id INT NOT NULL,
                category_id INT NOT NULL,
                PRIMARY KEY(
                    heureka_category_id, category_id
                )
            )');
        $this->sql('CREATE INDEX IDX_FE112A6925DFF6E0 ON heureka_category_categories (heureka_category_id)');
        $this->sql('CREATE UNIQUE INDEX UNIQ_FE112A6912469DE2 ON heureka_category_categories (category_id)');
        $this->sql('
            ALTER TABLE
                heureka_category_categories
            ADD
                CONSTRAINT FK_FE112A6925DFF6E0 FOREIGN KEY (heureka_category_id) REFERENCES heureka_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->sql('
            ALTER TABLE
                heureka_category_categories
            ADD
                CONSTRAINT FK_FE112A6912469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
