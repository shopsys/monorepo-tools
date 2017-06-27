<?php

namespace Shopsys\ShopBundle\DataFixtures\Base;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractNativeFixture;
use Shopsys\ShopBundle\DataFixtures\Base\DomainDbFunctionsDataFixture;

class FulltextTriggersDataFixture extends AbstractNativeFixture implements DependentFixtureInterface
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->createProductCatnumTrigger();
        $this->createProductPartnoTrigger();
        $this->createProductTranslationNameTrigger();
        $this->createProductDomainDescriptionTrigger();

        $this->createProductDomainFulltextTriggerOnProduct();
        $this->createProductDomainFulltextTriggerOnProductTranslation();
        $this->createProductDomainFulltextTriggerOnProductDomain();
    }

    private function createProductCatnumTrigger()
    {
        $this->executeNativeQuery('
            CREATE OR REPLACE FUNCTION set_product_catnum_tsvector() RETURNS trigger AS $$
                BEGIN
                    NEW.catnum_tsvector := to_tsvector(coalesce(NEW.catnum, \'\'));
                    RETURN NEW;
                END;
            $$ LANGUAGE plpgsql;
        ');

        $this->executeNativeQuery('
            CREATE TRIGGER recalc_catnum_tsvector
            BEFORE INSERT OR UPDATE OF catnum
            ON products
            FOR EACH ROW
            EXECUTE PROCEDURE set_product_catnum_tsvector();
        ');
    }

    private function createProductPartnoTrigger()
    {
        $this->executeNativeQuery('
            CREATE OR REPLACE FUNCTION set_product_partno_tsvector() RETURNS trigger AS $$
                BEGIN
                    NEW.partno_tsvector := to_tsvector(coalesce(NEW.partno, \'\'));
                    RETURN NEW;
                END;
            $$ LANGUAGE plpgsql;
        ');

        $this->executeNativeQuery('
            CREATE TRIGGER recalc_partno_tsvector
            BEFORE INSERT OR UPDATE OF partno
            ON products
            FOR EACH ROW
            EXECUTE PROCEDURE set_product_partno_tsvector();
        ');
    }

    private function createProductTranslationNameTrigger()
    {
        $this->executeNativeQuery('
            CREATE OR REPLACE FUNCTION set_product_translation_name_tsvector() RETURNS trigger AS $$
                BEGIN
                    NEW.name_tsvector := to_tsvector(coalesce(NEW.name, \'\'));
                    RETURN NEW;
                END;
            $$ LANGUAGE plpgsql;
        ');

        $this->executeNativeQuery('
            CREATE TRIGGER recalc_name_tsvector
            BEFORE INSERT OR UPDATE OF name
            ON product_translations
            FOR EACH ROW
            EXECUTE PROCEDURE set_product_translation_name_tsvector();
        ');
    }

    private function createProductDomainDescriptionTrigger()
    {
        $this->executeNativeQuery('
            CREATE OR REPLACE FUNCTION set_product_domain_description_tsvector() RETURNS trigger AS $$
                BEGIN
                    NEW.description_tsvector := to_tsvector(coalesce(NEW.description, \'\'));
                    RETURN NEW;
                END;
            $$ LANGUAGE plpgsql;
        ');

        $this->executeNativeQuery('
            CREATE TRIGGER recalc_description_tsvector
            BEFORE INSERT OR UPDATE OF description
            ON product_domains
            FOR EACH ROW
            EXECUTE PROCEDURE set_product_domain_description_tsvector();
        ');
    }

    private function createProductDomainFulltextTriggerOnProduct()
    {
        $this->executeNativeQuery('
            CREATE OR REPLACE FUNCTION update_product_domain_fulltext_tsvector_by_product() RETURNS trigger AS $$
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

        $this->executeNativeQuery('
            CREATE TRIGGER recalc_product_domain_fulltext_tsvector
            AFTER INSERT OR UPDATE OF catnum, partno
            ON products
            FOR EACH ROW
            EXECUTE PROCEDURE update_product_domain_fulltext_tsvector_by_product();
        ');
    }

    private function createProductDomainFulltextTriggerOnProductTranslation()
    {
        $this->executeNativeQuery('
            CREATE OR REPLACE FUNCTION update_product_domain_fulltext_tsvector_by_product_translation() RETURNS trigger AS $$
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

        $this->executeNativeQuery('
            CREATE TRIGGER recalc_product_domain_fulltext_tsvector
            AFTER INSERT OR UPDATE OF name
            ON product_translations
            FOR EACH ROW
            EXECUTE PROCEDURE update_product_domain_fulltext_tsvector_by_product_translation();
        ');
    }

    private function createProductDomainFulltextTriggerOnProductDomain()
    {
        $this->executeNativeQuery('
            CREATE OR REPLACE FUNCTION set_product_domain_fulltext_tsvector() RETURNS trigger AS $$
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

        $this->executeNativeQuery('
            DROP TRIGGER IF EXISTS recalc_product_domain_fulltext_tsvector on product_domains;
        ');
        $this->executeNativeQuery('
            CREATE TRIGGER recalc_product_domain_fulltext_tsvector
            BEFORE INSERT OR UPDATE OF description, short_description
            ON product_domains
            FOR EACH ROW
            EXECUTE PROCEDURE set_product_domain_fulltext_tsvector();
        ');
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            DomainDbFunctionsDataFixture::class,
        ];
    }
}
