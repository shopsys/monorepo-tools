<?php

namespace Shopsys\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use PDO;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20171005091354 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->transferDroppedHeurekaCategoryDataToPluginDataValues();
        $this->transferDroppedCategoryDataToPluginDataValues();

        $this->sql('ALTER TABLE categories DROP heureka_cz_feed_category_id');
        $this->sql('DROP TABLE feed_categories');
    }

    private function transferDroppedHeurekaCategoryDataToPluginDataValues()
    {
        $heurekaCategoryDataValues = [];
        $heurekaCategoryRows = $this->sql(
            'SELECT ext_id, name, full_name FROM feed_categories'
        )->fetchAll(PDO::FETCH_ASSOC);
        foreach ($heurekaCategoryRows as $row) {
            $heurekaCategoryDataValues[$row['ext_id']] = [
                'id' => $row['ext_id'],
                'name' => $row['name'],
                'full_name' => $row['full_name'],
            ];
        }

        $this->insertHeurekaDataValues($heurekaCategoryDataValues, 'heureka_category');
    }

    private function transferDroppedCategoryDataToPluginDataValues()
    {
        $categoryDataValues = [];
        $categoryRows = $this->sql(
            'SELECT categories.id, feed_categories.ext_id FROM categories
              JOIN feed_categories ON feed_categories.id = categories.heureka_cz_feed_category_id
              WHERE categories.heureka_cz_feed_category_id IS NOT NULL'
        )->fetchAll(PDO::FETCH_ASSOC);
        foreach ($categoryRows as $row) {
            $categoryDataValues[$row['id']] = [
                'heureka_category' => $row['ext_id'],
            ];
        }

        $this->insertHeurekaDataValues($categoryDataValues, 'category');
    }

    /**
     * @param array $valuesByKey
     * @param string $context
     */
    private function insertHeurekaDataValues(array $valuesByKey, $context)
    {
        foreach ($valuesByKey as $key => $value) {
            $this->sql(
                'INSERT INTO plugin_data_values (plugin_name, context, key, json_value) 
                VALUES (:pluginName, :context, :key, :jsonValue)',
                [
                    'pluginName' => 'Shopsys\\ProductFeed\\HeurekaBundle\\ShopsysProductFeedHeurekaBundle',
                    'context' => $context,
                    'key' => $key,
                    'jsonValue' => json_encode($value),
                ]
            );
        }
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
