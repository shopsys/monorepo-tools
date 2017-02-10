<?php

namespace Shopsys\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractNativeFixture;

class MainVariantPriceTriggerDataFixture extends AbstractNativeFixture {

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {
        $this->executeNativeQuery('
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

        $this->executeNativeQuery('
            CREATE TRIGGER recalc_main_variant_price
            AFTER INSERT OR UPDATE OF visible
            ON product_visibilities
            FOR EACH ROW
            EXECUTE PROCEDURE set_main_variant_price_recalculation_by_product_visibility();
        ');
    }

}
