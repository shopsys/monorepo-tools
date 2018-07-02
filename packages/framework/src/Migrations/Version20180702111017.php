<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180702111017 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('
            CREATE OR REPLACE FUNCTION set_main_variant_price_recalculation_by_product_visibility() RETURNS trigger AS $$
                BEGIN
                    UPDATE products p_main
                        SET recalculate_price = TRUE
                    FROM products p
                    WHERE p_main.id = p.main_variant_id
                        AND p.id = NEW.product_id;
                    RETURN NEW;
                END;
            $$ LANGUAGE plpgsql;
        ');

        $this->sql('DROP TRIGGER IF EXISTS recalc_main_variant_price ON product_visibilities');
        $this->sql('
            CREATE TRIGGER recalc_main_variant_price
            AFTER INSERT OR UPDATE OF visible
            ON product_visibilities
            FOR EACH ROW
            EXECUTE PROCEDURE set_main_variant_price_recalculation_by_product_visibility();
        ');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
