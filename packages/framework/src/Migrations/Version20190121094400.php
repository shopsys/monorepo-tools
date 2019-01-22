<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Shopsys\FrameworkBundle\Migrations\DataModifiers\CountryDataModifierVersion20190121094400;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20190121094400 extends AbstractMigration
{
    use MultidomainMigrationTrait;

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('
            CREATE TABLE country_translations (
                id SERIAL NOT NULL,
                translatable_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                locale VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            )');
        $this->sql('CREATE INDEX IDX_CA1456952C2AC5D3 ON country_translations (translatable_id)');
        $this->sql('
            CREATE UNIQUE INDEX country_translations_uniq_trans ON country_translations (translatable_id, locale)');
        $this->sql('
            CREATE TABLE country_domains (
                id SERIAL NOT NULL,
                country_id INT NOT NULL,
                domain_id INT NOT NULL,
                enabled BOOLEAN NOT NULL,
                priority INT NOT NULL DEFAULT 0,
                PRIMARY KEY(id)
            )');
        $this->sql('CREATE INDEX IDX_4537E7F0F92F3E70 ON country_domains (country_id)');
        $this->sql('CREATE UNIQUE INDEX country_domain ON country_domains (country_id, domain_id)');
        $this->sql('
            ALTER TABLE
                country_translations
            ADD
                CONSTRAINT FK_CA1456952C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES countries (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->sql('
            ALTER TABLE
                country_domains
            ADD
                CONSTRAINT FK_4537E7F0F92F3E70 FOREIGN KEY (country_id) REFERENCES countries (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->sql('ALTER TABLE countries ALTER COLUMN name DROP NOT NULL');
        $this->sql('ALTER TABLE countries ALTER COLUMN domain_id DROP NOT NULL');

        $countries = $this->sql('SELECT * FROM countries')->fetchAll();

        $transformer = new CountryDataModifierVersion20190121094400($countries);

        foreach ($this->getAllDomainIds() as $domainId) {
            foreach ($transformer->getAllIds() as $oldId) {
                $newId = $transformer->getNewId($oldId);

                $this->sql(
                    'UPDATE orders SET country_id = :newCountryId WHERE country_id = :originalCountryId AND domain_id = :domainId',
                    [
                        'newCountryId' => $newId,
                        'originalCountryId' => $oldId,
                        'domainId' => $domainId,
                    ]
                );

                $this->sql(
                    'UPDATE orders SET delivery_country_id = :newCountryId WHERE delivery_country_id = :originalCountryId AND domain_id = :domainId',
                    [
                        'newCountryId' => $newId,
                        'originalCountryId' => $oldId,
                        'domainId' => $domainId,
                    ]
                );

                $this->sql(
                    'UPDATE billing_addresses SET country_id = :newCountryId WHERE country_id = :originalCountryId',
                    [
                        'newCountryId' => $newId,
                        'originalCountryId' => $oldId,
                    ]
                );

                $this->sql(
                    'UPDATE delivery_addresses SET country_id = :newCountryId WHERE country_id = :originalCountryId',
                    [
                        'newCountryId' => $newId,
                        'originalCountryId' => $oldId,
                    ]
                );
            }

            foreach ($transformer->getAllCodes() as $code) {
                $domainData = $transformer->getDomainDataForCountry($domainId, $code);
                $this->sql('INSERT INTO country_domains (country_id, domain_id, enabled) VALUES (:countryId, :domainId, :enabled)', [$domainData['country_id'], $domainData['domain_id'], (int)$domainData['enabled']]);

                $translatableData = $transformer->getTranslatableDataForCountry($domainId, $code);
                $this->sql('INSERT INTO country_translations (translatable_id, name, locale) VALUES (:translatableId, :name, :locale)', [$translatableData['translatable_id'], $translatableData['name'], $this->getDomainLocale($domainId)]);
            }
        }

        $this->sql('DELETE FROM countries WHERE id IN (:ids)', ['ids' => $transformer->getObsoleteCountryIds()], ['ids' => Connection::PARAM_INT_ARRAY]);

        $this->sql('ALTER TABLE countries DROP COLUMN name');
        $this->sql('ALTER TABLE countries DROP COLUMN domain_id');
        $this->sql('ALTER TABLE countries ALTER code SET NOT NULL');
        $this->sql('ALTER TABLE country_domains ALTER priority DROP DEFAULT');
        $this->sql('CREATE UNIQUE INDEX countries_code_uni ON countries (code)');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
