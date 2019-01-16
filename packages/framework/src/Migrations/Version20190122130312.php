<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20190122130312 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('
            CREATE TABLE carts (
                id SERIAL NOT NULL,
                user_id INT DEFAULT NULL,
                cart_identifier VARCHAR(127) NOT NULL,
                modified_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                PRIMARY KEY(id)
            )');
        $this->sql('CREATE INDEX IDX_4E004AACA76ED395 ON carts (user_id)');
        $this->sql('
            ALTER TABLE
                carts
            ADD
                CONSTRAINT FK_4E004AACA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->sql('ALTER TABLE cart_items ADD cart_id INT NOT NULL DEFAULT 0');

        $this->sql('
            INSERT INTO carts (cart_identifier, user_id, modified_at)
            WITH CI AS (
                SELECT 
                    cart_identifier, 
                    user_id, added_at, 
                    ROW_NUMBER() OVER(PARTITION BY cart_identifier, user_id ORDER BY added_at DESC) AS row_number 
                FROM cart_items
            )
            SELECT 
                cart_identifier, 
                user_id, 
                added_at 
            FROM CI 
            WHERE 
                row_number = 1');

        $this->sql('
            UPDATE cart_items 
            SET cart_id = C.id 
            FROM carts C 
            WHERE cart_items.user_id = C.user_id AND cart_items.cart_identifier = C.cart_identifier
        ');

        $this->sql('
            UPDATE cart_items 
            SET cart_id = C.id 
            FROM carts C 
            WHERE cart_items.user_id IS NULL AND C.user_id IS NULL AND cart_items.cart_identifier = C.cart_identifier
        ');

        $this->sql('ALTER TABLE cart_items DROP user_id, DROP cart_identifier');
        $this->sql('ALTER TABLE cart_items ALTER cart_id DROP DEFAULT');
        $this->sql('
            ALTER TABLE
                cart_items
            ADD
                CONSTRAINT FK_BEF484451AD5CDBF FOREIGN KEY (cart_id) REFERENCES carts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->sql('CREATE INDEX IDX_BEF484451AD5CDBF ON cart_items (cart_id)');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
