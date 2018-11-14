<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

final class Version20181114134959 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema) : void
    {
        /**
         * for each price group find matching currency on price group domain
         * this results in group_coef and currency coef
         *
         * for each product and each pricing group
         * create manual price with value (price * group_coef / currency coef)
         */
        $this->sql(
            'INSERT INTO product_manual_input_prices (product_id, pricing_group_id, input_price)
                SELECT p.id, t.group_id, p.price * t.group_coef / t.currency_coef price
                FROM products p
                CROSS JOIN 
                (
                    SELECT g.id group_id, g.coefficient group_coef, c.exchange_rate currency_coef
                    FROM setting_values s
                    JOIN pricing_groups g ON g.domain_id = s.domain_id
                    JOIN currencies c ON c.id = s.value::integer
                    WHERE s.name = \'defaultDomainCurrencyId\'
                ) t
                WHERE p.price_calculation_type = \'auto\''
        );
        $this->sql('UPDATE products SET price_calculation_type = \'manual\' WHERE price_calculation_type = \'auto\'');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema) : void
    {
    }
}
