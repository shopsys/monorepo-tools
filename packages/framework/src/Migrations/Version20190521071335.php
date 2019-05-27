<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20190521071335 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('ALTER TABLE products ADD uuid UUID DEFAULT NULL');
        $this->sql('UPDATE products SET uuid = uuid_generate_v4()');
        $this->sql('ALTER TABLE products ALTER uuid SET NOT NULL');
        $this->sql('CREATE UNIQUE INDEX UNIQ_B3BA5A5AD17F50A6 ON products (uuid)');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
