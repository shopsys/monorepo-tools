<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180603135341 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $orderStatusesCount = $this->sql('SELECT count(*) FROM order_statuses')->fetchColumn(0);
        if ($orderStatusesCount > 0) {
            return;
        }
        $this->createOrderStatusWithEnglishAndCzechTranslations(1, 1, 'New', 'Nová');
        $this->createOrderStatusWithEnglishAndCzechTranslations(2, 2, 'In Progress', 'Vyřizuje se');
        $this->createOrderStatusWithEnglishAndCzechTranslations(3, 3, 'Done', 'Vyřízena');
        $this->createOrderStatusWithEnglishAndCzechTranslations(4, 4, 'Canceled', 'Stornována');
        $this->sql('ALTER SEQUENCE order_statuses_id_seq RESTART WITH 5');
    }

    /**
     * @param int $orderStatusId
     * @param int $orderStatusType
     * @param string $orderStatusEnglishName
     * @param string $orderStatusCzechName
     */
    private function createOrderStatusWithEnglishAndCzechTranslations(
        $orderStatusId,
        $orderStatusType,
        $orderStatusEnglishName,
        $orderStatusCzechName
    ) {
        $this->sql('INSERT INTO order_statuses (id, type) VALUES (:id, :type)', [
            'id' => $orderStatusId,
            'type' => $orderStatusType,
        ]);
        $this->sql('INSERT INTO order_status_translations (translatable_id, name, locale) VALUES (:translatableId, :name, :locale)', [
            'translatableId' => $orderStatusId,
            'name' => $orderStatusEnglishName,
            'locale' => 'en',
        ]);
        $this->sql('INSERT INTO order_status_translations (translatable_id, name, locale) VALUES (:translatableId, :name, :locale)', [
            'translatableId' => $orderStatusId,
            'name' => $orderStatusCzechName,
            'locale' => 'cs',
        ]);
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
