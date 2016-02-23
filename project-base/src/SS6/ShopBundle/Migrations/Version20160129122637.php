<?php

namespace SS6\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;
use SS6\ShopBundle\Component\Setting\Setting;

class Version20160129122637 extends AbstractMigration {

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 */
	public function up(Schema $schema) {
		$sql = 'INSERT INTO settings3 (name, domain_id, value, type) VALUES
			(\'' . Setting::BASE_URL . '\', 1, \'http://localhost:8080\', \'string\'),
			(\'' . Setting::BASE_URL . '\', 2, \'http://2.localhost:8080\', \'string\')';
		$this->sql($sql);
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 */
	public function down(Schema $schema) {

	}

}
