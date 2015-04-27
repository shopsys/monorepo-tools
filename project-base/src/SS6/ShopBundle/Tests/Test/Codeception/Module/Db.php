<?php

namespace SS6\ShopBundle\Tests\Test\Codeception\Module;

use Codeception\Module\Db as BaseDb;

class Db extends BaseDb {

	/**
	 * Revert database to the original state
	 *
	 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
	 */
	// @codingStandardsIgnoreStart
	public function _afterSuite() {
	// @codingStandardsIgnoreEnd
		$this->cleanup();
		$this->loadDump();
	}

}
