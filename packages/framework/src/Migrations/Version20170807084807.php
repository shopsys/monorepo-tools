<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use PDO;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20170807084807 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->transferDroppedProductDataToPluginDataValues();

        $this->sql('ALTER TABLE product_domains DROP heureka_cpc');
        $this->sql('ALTER TABLE product_domains DROP show_in_zbozi_feed');
        $this->sql('ALTER TABLE product_domains DROP zbozi_cpc');
        $this->sql('ALTER TABLE product_domains DROP zbozi_cpc_search');
    }

    private function transferDroppedProductDataToPluginDataValues()
    {
        $heurekaDataValues = [];
        $zboziDataValues = [];
        $productDomainRows = $this->sql(
            'SELECT product_id, domain_id, heureka_cpc, zbozi_cpc, zbozi_cpc_search, show_in_zbozi_feed
            FROM product_domains'
        )->fetchAll(PDO::FETCH_ASSOC);
        foreach ($productDomainRows as $row) {
            $heurekaDataValues[$row['product_id']]['cpc'][$row['domain_id']] = $row['heureka_cpc'];

            $zboziDataValues[$row['product_id']]['cpc'][$row['domain_id']] = $row['zbozi_cpc'];
            $zboziDataValues[$row['product_id']]['cpc_search'][$row['domain_id']] = $row['zbozi_cpc_search'];
            $zboziDataValues[$row['product_id']]['show'][$row['domain_id']] = $row['show_in_zbozi_feed'];
        }

        $this->insertPluginDataValues(
            $heurekaDataValues,
            'Shopsys\\ProductFeed\\HeurekaBundle\\ShopsysProductFeedHeurekaBundle'
        );
        $this->insertPluginDataValues(
            $zboziDataValues,
            'Shopsys\\ProductFeed\\ZboziBundle\\ShopsysProductFeedZboziBundle'
        );
    }

    /**
     * @param array $valuesByKey
     * @param string $pluginName
     */
    private function insertPluginDataValues(array $valuesByKey, $pluginName)
    {
        foreach ($valuesByKey as $key => $value) {
            $this->sql(
                'INSERT INTO plugin_data_values (plugin_name, context, key, json_value) 
                VALUES (:pluginName, :context, :key, :jsonValue)',
                [
                    'pluginName' => $pluginName,
                    'context' => 'product',
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
