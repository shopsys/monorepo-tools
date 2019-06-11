<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use PDO;
use RuntimeException;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20190611114955 extends AbstractMigration
{
    protected const MAX_LISTED_ORDERS = 10;

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->sql('ALTER TABLE order_items ADD total_price_without_vat NUMERIC(20, 6) DEFAULT NULL');
        $this->sql('ALTER TABLE order_items ADD total_price_with_vat NUMERIC(20, 6) DEFAULT NULL');
        $this->sql('COMMENT ON COLUMN order_items.total_price_without_vat IS \'(DC2Type:money)\'');
        $this->sql('COMMENT ON COLUMN order_items.total_price_with_vat IS \'(DC2Type:money)\'');

        $this->calculateOrderItemTotalPrices();
        $this->validateOrderItemTotalPrices();
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema): void
    {
    }

    /**
     * Calculates the total prices of order items using via old price calculation (using VAT coefficients)
     * @see https://github.com/shopsys/shopsys/blob/v7.2.1/packages/framework/src/Model/Order/Item/OrderItemPriceCalculation.php#L60-L76
     * @see https://github.com/shopsys/shopsys/blob/v7.2.1/packages/framework/src/Model/Pricing/PriceCalculation.php#L25-L46
     */
    protected function calculateOrderItemTotalPrices(): void
    {
        $this->sql('UPDATE order_items SET total_price_with_vat = price_with_vat * quantity');
        $this->sql('UPDATE order_items SET total_price_without_vat = total_price_with_vat - ROUND(total_price_with_vat * ROUND(vat_percent / (100 + vat_percent), 4), 2)');
    }

    /**
     * Validates that the calculated order item total prices add up to the total price of the order
     */
    protected function validateOrderItemTotalPrices(): void
    {
        $statement = $this->connection->query(
            'SELECT o.id, o.total_price_with_vat, order_item_total_prices.with_vat, o.total_price_without_vat, order_item_total_prices.without_vat
            FROM orders o
            LEFT JOIN (
                SELECT order_id, SUM(total_price_with_vat) AS with_vat, SUM(total_price_without_vat) AS without_vat
                FROM order_items
                GROUP BY order_id
            ) order_item_total_prices ON order_item_total_prices.order_id = o.id
            WHERE o.total_price_with_vat != order_item_total_prices.with_vat OR o.total_price_without_vat != order_item_total_prices.without_vat
            ORDER BY o.id'
        );
        $incorrectOrderCount = $statement->rowCount();

        if ($incorrectOrderCount > 0) {
            $message = sprintf('There are %d orders in your DB where the order item total prices do not add up to the total price of the order:', $incorrectOrderCount);

            for ($i = 0; $i < min($incorrectOrderCount, static::MAX_LISTED_ORDERS); $i++) {
                $incorrectOrder = $statement->fetch(PDO::FETCH_NUM);

                $message .= sprintf("\n  - ID %d: order total %s, sum %s (with VAT); order total %s, sum %s (without VAT)", ...$incorrectOrder);
            }

            if ($incorrectOrderCount > static::MAX_LISTED_ORDERS) {
                $message .= "\n  ...";
            }

            $message .= "\n\nYou'll have to create your own DB migration to calculate the order item total prices according to your price calculation and skip this migration.";
            $message .= "\n\nIf you have modified the VAT calculation or rounding you can extend this class and override 'calculateOrderItemTotalPrices()'.";
            $message .= "\n\nSee https://github.com/shopsys/shopsys/blob/v7.2.1/docs/introduction/database-migrations.md for details.";

            throw new RuntimeException($message);
        }
    }
}
