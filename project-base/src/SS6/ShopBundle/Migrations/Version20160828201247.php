<?php

namespace SS6\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;
use SS6\ShopBundle\Model\Script\Script;

class Version20160828201247 extends AbstractMigration {

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 */
	public function up(Schema $schema) {
		$this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES
			(\'' . Script::GOOGLE_ANALYTICS_TRACKING_ID_SETTING_NAME . '\', 1, null, \'string\');
		');
		$this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES
			(\'' . Script::GOOGLE_ANALYTICS_TRACKING_ID_SETTING_NAME . '\', 2, null, \'string\');
		');
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 */
	public function down(Schema $schema) {
	}

}
