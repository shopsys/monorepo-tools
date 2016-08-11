<?php

namespace SS6\ShopBundle\Component\Migration;

use PDO;
use SS6\ShopBundle\Component\Setting\Setting;

/**
 * This trait can be used in classes
 * that extend \ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration.
 */
trait MultidomainMigrationTrait {

	/**
	 * @return int[]
	 */
	protected function getAllDomainIds() {
		return $this
			->sql('SELECT domain_id FROM setting_values WHERE name = :baseUrl', ['baseUrl' => Setting::BASE_URL])
			->fetchAll(PDO::FETCH_COLUMN, 'domain_id');
	}

	/**
	 * @param int $domainId
	 * @return string
	 */
	protected function getDomainLocale($domainId) {
		return $this
			->sql('SELECT get_domain_locale(:domainId)', ['domainId' => $domainId])
			->fetchColumn();
	}

}
