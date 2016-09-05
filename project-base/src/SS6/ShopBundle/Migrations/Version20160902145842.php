<?php

namespace SS6\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20160902145842 extends AbstractMigration {

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 */
	public function up(Schema $schema) {
		$sql = 'INSERT INTO setting_values VALUES
			(\'heurekaWidgetCode\', 1, null, \'string\'),
			(\'heurekaWidgetCode\', 2, null, \'string\');';
		$this->sql($sql);
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 */
	public function down(Schema $schema) {

	}

}
