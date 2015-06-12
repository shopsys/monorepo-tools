<?php

namespace SS6\ShopBundle\Tests\Test\Codeception\Module;

use Codeception\Module\Db as BaseDb;

class Db extends BaseDb {

	// @codingStandardsIgnoreStart
	/**
	 * Revert database to the original state
	 *
	 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
	 */
	public function _afterSuite() {
	// @codingStandardsIgnoreEnd
		$this->cleanup();
		$this->loadDump();
	}

}
