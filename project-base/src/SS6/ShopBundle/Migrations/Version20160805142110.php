<?php

namespace SS6\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20160805142110 extends AbstractMigration {

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 */
	public function up(Schema $schema) {
		$this->sql('ALTER TABLE product_domains ADD show_in_zbozi_feed BOOLEAN NOT NULL DEFAULT TRUE');
		$this->sql('ALTER TABLE product_domains ALTER show_in_zbozi_feed DROP DEFAULT');
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 */
	public function down(Schema $schema) {
	}

}
