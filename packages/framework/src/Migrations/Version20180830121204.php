<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180830121204 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('ALTER TABLE users ADD telephone VARCHAR(30) DEFAULT NULL');

        $phonesAndBillingAddressesIds = $this->sql('SELECT telephone, id FROM billing_addresses')->fetchAll();
        foreach ($phonesAndBillingAddressesIds as $phoneAndBillingAddressId) {
            $this->sql(
                'UPDATE users
                SET telephone = :telephone
                WHERE billing_address_id = :billing_address_id',
                [
                    'telephone' => $phoneAndBillingAddressId['telephone'],
                    'billing_address_id' => $phoneAndBillingAddressId['id'],
                ]
            );
        }
        $this->sql('ALTER TABLE billing_addresses DROP COLUMN telephone');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
