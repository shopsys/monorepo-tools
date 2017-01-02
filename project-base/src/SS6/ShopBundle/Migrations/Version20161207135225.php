<?php

namespace SS6\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;
use SS6\ShopBundle\Component\Migration\MultidomainMigrationTrait;

class Version20161207135225 extends AbstractMigration {

	use MultidomainMigrationTrait;

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 */
	public function up(Schema $schema) {
		foreach ($this->getAllDomainIds() as $domainId) {
			$this->sql('DELETE FROM migrations WHERE version = \'201601207135225\';');

			$phoneNumber = $this->sql('SELECT COUNT(*) FROM setting_values WHERE name = \'shopInfoPhoneNumber\' AND domain_id = :domainId;
			', ['domainId' => $domainId])->fetchColumn(0);
			$infoMail = $this->sql('SELECT COUNT(*) FROM setting_values WHERE name = \'shopInfoEmail\' AND domain_id = :domainId;
			', ['domainId' => $domainId])->fetchColumn(0);

			if ($phoneNumber <= 0) {
				$this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES
					(\'shopInfoPhoneNumber\', :domainId, \'+420123456789\', \'string\');
				', ['domainId' => $domainId]);
			}
			if ($infoMail <= 0) {
				$this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES
					(\'shopInfoEmail\', :domainId, \'no-reply@shopsys.com\', \'string\');
				', ['domainId' => $domainId]);
			}
		}
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 */
	public function down(Schema $schema) {

	}

}
