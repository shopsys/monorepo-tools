<?php

namespace Shopsys\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20160322180746 extends AbstractMigration {

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 */
	public function up(Schema $schema) {
		$this->sql('ALTER TABLE parameter_titles_translations RENAME TO parameter_translations;');
		$this->sql('ALTER TABLE availabilities_translations RENAME TO availability_translations;');
		$this->sql('ALTER TABLE flags_translations RENAME TO flag_translations;');
		$this->sql('ALTER TABLE units_translations RENAME TO unit_translations;');

		$this->sql('ALTER INDEX idx_23d1ba1a2c2ac5d3 RENAME TO IDX_2100ABA2C2AC5D3;');
		$this->sql('ALTER INDEX flags_translations_uniq_trans RENAME TO flag_translations_uniq_trans;');
		$this->sql('ALTER INDEX idx_16f42d262c2ac5d3 RENAME TO IDX_77C2A7492C2AC5D3;');
		$this->sql('ALTER INDEX parameter_titles_translations_uniq_trans RENAME TO parameter_translations_uniq_trans;');
		$this->sql('ALTER INDEX idx_80d708562c2ac5d3 RENAME TO IDX_670A3D112C2AC5D3;');
		$this->sql('ALTER INDEX availabilities_translations_uniq_trans RENAME TO availability_translations_uniq_trans;');
		$this->sql('ALTER INDEX idx_15c4c1de2c2ac5d3 RENAME TO IDX_142138102C2AC5D3;');
		$this->sql('ALTER INDEX units_translations_uniq_trans RENAME TO unit_translations_uniq_trans;');

		$this->sql('ALTER SEQUENCE units_translations_id_seq RENAME TO unit_translations_id_seq');
		$this->sql('ALTER SEQUENCE flags_translations_id_seq RENAME TO flag_translations_id_seq');
		$this->sql('ALTER SEQUENCE availabilities_translations_id_seq RENAME TO availability_translations_id_seq');
		$this->sql('ALTER SEQUENCE parameter_titles_translations_id_seq RENAME TO parameter_translations_id_seq');
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 */
	public function down(Schema $schema) {

	}

}
