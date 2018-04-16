<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use PDO;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180413102103 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $oldTableExists = $this->sql(
            'SELECT COUNT(*) > 0 FROM information_schema.tables WHERE table_name=\'plugin_data_values\''
        )->fetchColumn();

        if ($oldTableExists) {
            $this->migrateProducts();
            $this->migrateHeurekaCategories();
            $this->migrateHeurekaCategoryToCategoryLinks();
        }
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }

    private function migrateProducts(): void
    {
        $rows = $this->sql(
            'SELECT key, json_value
            FROM plugin_data_values
            WHERE plugin_name=:plugin_name AND context=:context',
            [
                'plugin_name' => 'Shopsys\\ProductFeed\\HeurekaBundle\\ShopsysProductFeedHeurekaBundle',
                'context' => 'product',
            ]
        )->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $jsonData = json_decode($row['json_value'], true);
            foreach ($jsonData['cpc'] ?? [] as $domainId => $cpc) {
                $this->sql(
                    'INSERT INTO heureka_product_domains (product_id, domain_id, cpc) 
                        VALUES (:product_id, :domain_id, :cpc)',
                    [
                        'product_id' => $row['key'],
                        'domain_id' => $domainId,
                        'cpc' => $cpc,
                    ]
                );
            }
        }
    }

    private function migrateHeurekaCategories(): void
    {
        $rows = $this->sql(
            'SELECT key, json_value
            FROM plugin_data_values
            WHERE plugin_name=:plugin_name AND context=:context',
            [
                'plugin_name' => 'Shopsys\\ProductFeed\\HeurekaBundle\\ShopsysProductFeedHeurekaBundle',
                'context' => 'heureka_category',
            ]
        )->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $jsonData = json_decode($row['json_value'], true);
            $this->sql(
                'INSERT INTO heureka_category (id, name, full_name)
                    VALUES (:id, :name, :full_name)',
                $jsonData
            );
        }
    }

    private function migrateHeurekaCategoryToCategoryLinks(): void
    {
        $rows = $this->sql(
            'SELECT key, json_value
            FROM plugin_data_values
            WHERE plugin_name=:plugin_name AND context=:context',
            [
                'plugin_name' => 'Shopsys\\ProductFeed\\HeurekaBundle\\ShopsysProductFeedHeurekaBundle',
                'context' => 'category',
            ]
        )->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $jsonData = json_decode($row['json_value'], true);
            if (array_key_exists('heureka_category', $jsonData)) {
                $this->sql(
                    'INSERT INTO heureka_category_categories (heureka_category_id, category_id)
                        VALUES (:heureka_category_id, :category_id)',
                    [
                        'heureka_category_id' => $jsonData['heureka_category'],
                        'category_id' => $row['key'],
                    ]
                );
            }
        }
    }
}
