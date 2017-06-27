<?php

namespace Shopsys\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20170627144556 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->replaceProductDomainFulltextTriggerOnProduct();
        $this->replaceProductDomainFulltextTriggerOnProductTranslation();
        $this->replaceProductDomainFulltextTriggerOnProductDomain();

        // This runs triggers in order to update fulltext columns
        $this->sql('UPDATE product_domains SET short_description = short_description');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }

    private function replaceProductDomainFulltextTriggerOnProduct()
    {
        $this->sql('
            REPLACE FUNCTION update_product_domain_fulltext_tsvector_by_product() RETURNS trigger AS $$
                BEGIN
                    UPDATE product_domains pd
                        SET fulltext_tsvector =
                            (
                                to_tsvector(COALESCE(pt.name, \'\'))
                                ||
                                to_tsvector(COALESCE(NEW.catnum, \'\'))
                                ||
                                to_tsvector(COALESCE(NEW.partno, \'\'))
                                ||
                                to_tsvector(COALESCE(pd.description, \'\'))
                                ||
                                to_tsvector(COALESCE(pd.short_description, \'\'))
                            )
                    FROM product_translations pt
                    WHERE pt.translatable_id = NEW.id
                        AND pt.locale = get_domain_locale(pd.domain_id)
                        AND pd.product_id = NEW.id;
                    RETURN NEW;
                END;
            $$ LANGUAGE plpgsql;
        ');
    }

    private function replaceProductDomainFulltextTriggerOnProductTranslation()
    {
        $this->sql('
            REPLACE FUNCTION update_product_domain_fulltext_tsvector_by_product_translation() RETURNS trigger AS $$
                BEGIN
                    UPDATE product_domains pd
                        SET fulltext_tsvector =
                            (
                                to_tsvector(COALESCE(NEW.name, \'\'))
                                ||
                                to_tsvector(COALESCE(p.catnum, \'\'))
                                ||
                                to_tsvector(COALESCE(p.partno, \'\'))
                                ||
                                to_tsvector(COALESCE(pd.description, \'\'))
                                ||
                                to_tsvector(COALESCE(pd.short_description, \'\'))
                            )
                    FROM products p
                    WHERE p.id = NEW.translatable_id
                        AND pd.product_id = NEW.translatable_id
                        AND pd.domain_id IN (SELECT * FROM get_domain_ids_by_locale(NEW.locale));
                    RETURN NEW;
                END;
            $$ LANGUAGE plpgsql;
        ');
    }

    private function replaceProductDomainFulltextTriggerOnProductDomain()
    {
        $this->sql('
            REPLACE FUNCTION set_product_domain_fulltext_tsvector() RETURNS trigger AS $$
                BEGIN
                    NEW.fulltext_tsvector :=
                        (
                            SELECT
                                to_tsvector(COALESCE(pt.name, \'\'))
                                ||
                                to_tsvector(COALESCE(p.catnum, \'\'))
                                ||
                                to_tsvector(COALESCE(p.partno, \'\'))
                                ||
                                to_tsvector(COALESCE(NEW.description, \'\'))
                                ||
                                to_tsvector(COALESCE(NEW.short_description, \'\'))
                            FROM products p
                            LEFT JOIN product_translations pt ON pt.translatable_id = p.id
                                AND pt.locale = get_domain_locale(NEW.domain_id)
                            WHERE p.id = NEW.product_id
                        );

                    RETURN NEW;
                END;
            $$ LANGUAGE plpgsql;
        ');

        $this->sql('
            DROP TRIGGER IF EXISTS recalc_product_domain_fulltext_tsvector on product_domains;
        ');
        $this->sql('
            CREATE TRIGGER recalc_product_domain_fulltext_tsvector
            BEFORE INSERT OR UPDATE OF description, short_description
            ON product_domains
            FOR EACH ROW
            EXECUTE PROCEDURE set_product_domain_fulltext_tsvector();
        ');
    }
}
