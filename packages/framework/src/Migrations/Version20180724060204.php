<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180724060204 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->sql('
            CREATE OR REPLACE FUNCTION set_product_catnum_tsvector() RETURNS trigger AS $$
                BEGIN
                    IF (TG_OP = \'INSERT\') OR (TG_OP = \'UPDATE\' AND (OLD.catnum IS DISTINCT FROM NEW.catnum)) THEN
                        NEW.catnum_tsvector := to_tsvector(coalesce(NEW.catnum, \'\'));
                        RETURN NEW;
                    END IF;
                    RETURN NULL;
                END;
            $$ LANGUAGE plpgsql;
        ');

        $this->sql('
            CREATE OR REPLACE FUNCTION set_product_partno_tsvector() RETURNS trigger AS $$
                BEGIN
                    IF (TG_OP = \'INSERT\') OR (TG_OP = \'UPDATE\' AND (OLD.partno IS DISTINCT FROM NEW.partno)) THEN
                        NEW.partno_tsvector := to_tsvector(coalesce(NEW.partno, \'\'));
                        RETURN NEW;
                    END IF;
                    RETURN NULL;
                END;
            $$ LANGUAGE plpgsql;
        ');

        $this->sql('
            CREATE OR REPLACE FUNCTION set_product_translation_name_tsvector() RETURNS trigger AS $$
                BEGIN
                    IF (TG_OP = \'INSERT\') OR (TG_OP = \'UPDATE\' AND (OLD.name IS DISTINCT FROM NEW.name)) THEN
                        NEW.name_tsvector := to_tsvector(coalesce(NEW.name, \'\'));
                        RETURN NEW;
                    END IF;
                    RETURN NULL;
                END;
            $$ LANGUAGE plpgsql;
        ');

        $this->sql('
            CREATE OR REPLACE FUNCTION set_product_domain_description_tsvector() RETURNS trigger AS $$
                BEGIN
                    IF (TG_OP = \'INSERT\') OR (TG_OP = \'UPDATE\' AND (OLD.description IS DISTINCT FROM NEW.description)) THEN
                        NEW.description_tsvector := to_tsvector(coalesce(NEW.description, \'\'));
                        RETURN NEW;
                    END IF;
                    RETURN NULL;
                END;
            $$ LANGUAGE plpgsql;
        ');

        $this->sql('
            CREATE OR REPLACE FUNCTION set_product_domain_fulltext_tsvector() RETURNS trigger AS $$
                BEGIN
                    IF (TG_OP = \'INSERT\') OR (TG_OP = \'UPDATE\' AND 
                        (
                            OLD.description IS DISTINCT FROM NEW.description OR
                            OLD.short_description IS DISTINCT FROM NEW.short_description
                        )
                    ) THEN
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
                    END IF;
                    RETURN NULL;
                END;
            $$ LANGUAGE plpgsql;
        ');

        $this->sql('
            CREATE OR REPLACE FUNCTION update_product_domain_fulltext_tsvector_by_product_translation() RETURNS trigger AS $$
                BEGIN
                    IF (TG_OP = \'INSERT\') OR (TG_OP = \'UPDATE\' AND (OLD.name IS DISTINCT FROM NEW.name)) THEN
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
                    END IF;
                    RETURN NULL;
                END;
            $$ LANGUAGE plpgsql;
        ');

        $this->sql('
            CREATE OR REPLACE FUNCTION update_product_domain_fulltext_tsvector_by_product() RETURNS trigger AS $$
                BEGIN
                    IF (TG_OP = \'INSERT\') OR (TG_OP = \'UPDATE\' AND (
                        OLD.catnum IS DISTINCT FROM NEW.catnum OR 
                        OLD.partno IS DISTINCT FROM NEW.partno
                    )) THEN
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
                    END IF;
                    RETURN NULL;
                END;
            $$ LANGUAGE plpgsql;
        ');
    }

    public function down(Schema $schema) : void
    {
    }
}
