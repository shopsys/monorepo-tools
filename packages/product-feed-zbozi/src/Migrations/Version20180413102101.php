<?php

namespace Shopsys\ProductFeed\ZboziBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use PDO;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180413102101 extends AbstractMigration
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
                'plugin_name' => 'Shopsys\\ProductFeed\\ZboziBundle\\ShopsysProductFeedZboziBundle',
                'context' => 'product',
            ]
        )->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $jsonData = json_decode($row['json_value'], true);
            $domainIds = !empty($jsonData) ? array_keys($jsonData['cpc']) : [];
            foreach ($domainIds as $domainId) {
                $this->sql(
                    'INSERT INTO zbozi_product_domains (product_id, domain_id, show, cpc, cpc_search) 
                        VALUES (:product_id, :domain_id, :show, :cpc, :cpc_search)',
                    [
                        'product_id' => $row['key'],
                        'domain_id' => $domainId,
                        'show' => $jsonData['show'][$domainId] ? 'true' : 'false',
                        'cpc' => $jsonData['cpc'][$domainId],
                        'cpc_search' => $jsonData['cpc_search'][$domainId],
                    ]
                );
            }
        }
    }
}
