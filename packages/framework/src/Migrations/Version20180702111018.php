<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180702111018 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $orderNumberSequenceCount = $this->sql('SELECT count(*) FROM order_number_sequences')->fetchColumn(0);
        if ($orderNumberSequenceCount > 0) {
            return;
        }

        $this->sql('INSERT INTO order_number_sequences (id, number) VALUES (1, 0)');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
