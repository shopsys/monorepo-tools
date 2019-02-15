<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20190215092226 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('COMMENT ON COLUMN cart_items.watched_price IS \'(DC2Type:money)\'');
        $this->sql('COMMENT ON COLUMN order_items.price_without_vat IS \'(DC2Type:money)\'');
        $this->sql('COMMENT ON COLUMN order_items.price_with_vat IS \'(DC2Type:money)\'');
        $this->sql('COMMENT ON COLUMN orders.total_price_with_vat IS \'(DC2Type:money)\'');
        $this->sql('COMMENT ON COLUMN orders.total_price_without_vat IS \'(DC2Type:money)\'');
        $this->sql('COMMENT ON COLUMN orders.total_product_price_with_vat IS \'(DC2Type:money)\'');
        $this->sql('COMMENT ON COLUMN payment_prices.price IS \'(DC2Type:money)\'');
        $this->sql('COMMENT ON COLUMN product_calculated_prices.price_with_vat IS \'(DC2Type:money)\'');
        $this->sql('COMMENT ON COLUMN product_manual_input_prices.input_price IS \'(DC2Type:money)\'');
        $this->sql('COMMENT ON COLUMN transport_prices.price IS \'(DC2Type:money)\'');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
