<?php

namespace Shopsys\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20161013090245 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('ALTER TABLE delivery_addresses ADD first_name VARCHAR(100) DEFAULT NULL');
        $this->sql('ALTER TABLE delivery_addresses ADD last_name VARCHAR(100) DEFAULT NULL');
        $this->sql('UPDATE delivery_addresses
            SET first_name = substr(contact_person, 0, strpos(contact_person, \' \')),
            last_name = substr(contact_person, strpos(contact_person, \' \') + 1)
        ');
        $this->sql('ALTER TABLE delivery_addresses DROP contact_person');

        $this->sql('ALTER TABLE orders ADD delivery_first_name VARCHAR(100) NOT NULL');
        $this->sql('ALTER TABLE orders ADD delivery_last_name VARCHAR(100) NOT NULL');
        $this->sql('UPDATE orders
            SET delivery_first_name = substr(delivery_contact_person, 0, strpos(delivery_contact_person, \' \')),
            delivery_last_name = substr(delivery_contact_person, strpos(delivery_contact_person, \' \') + 1)
        ');
        $this->sql('ALTER TABLE orders DROP delivery_contact_person');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
