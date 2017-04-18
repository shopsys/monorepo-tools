<?php

namespace Shopsys\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20170418094333 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('
            ALTER TABLE payment_prices
                DROP CONSTRAINT FK_C1F3F6CF38248176,
                ADD CONSTRAINT FK_C1F3F6CF38248176 FOREIGN KEY (currency_id)
                    REFERENCES currencies (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
        $this->sql('
            ALTER TABLE transport_prices
                DROP CONSTRAINT FK_573018D038248176,
                ADD CONSTRAINT FK_573018D038248176 FOREIGN KEY (currency_id)
                    REFERENCES currencies (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
