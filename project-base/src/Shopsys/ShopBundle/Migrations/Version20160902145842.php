<?php

namespace SS6\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;
use SS6\ShopBundle\Component\Migration\MultidomainMigrationTrait;

class Version20160902145842 extends AbstractMigration {

	use MultidomainMigrationTrait;

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 */
	public function up(Schema $schema) {
		foreach ($this->getAllDomainIds() as $domainId) {
			$this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES
				(\'heurekaWidgetCode\', :domainId, null, \'string\');
			', ['domainId' => $domainId]);
		}
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 */
	public function down(Schema $schema) {

	}

}
