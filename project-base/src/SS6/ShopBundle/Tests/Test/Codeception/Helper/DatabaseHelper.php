<?php

namespace SS6\ShopBundle\Tests\Test\Codeception\Helper;

use Codeception\Module;
use Doctrine\DBAL\Connection;
use SS6\ShopBundle\Tests\Test\Codeception\Helper\SymfonyHelper;

class DatabaseHelper extends Module {

	/**
	 * {@inheritDoc}
	 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
	 */
	// @codingStandardsIgnoreStart
	public function _initialize() {
	// @codingStandardsIgnoreEnd
		$dbModule = $this->getModule('Db');
		/* @var $dbModule \Codeception\Module\Db */
		$symfonyHelper = $this->getModule(SymfonyHelper::class);
		/* @var $symfonyHelper \SS6\ShopBundle\Tests\Test\Codeception\Helper\SymfonyHelper */
		$connection = $symfonyHelper->grabServiceFromContainer('doctrine.dbal.default_connection');
		/* @var $connection \Doctrine\DBAL\Connection */

		$dbModule->_reconfigure([
			'dsn' => $this->getConnectionDsn($connection),
			'user' => $connection->getUsername(),
			'password' => $connection->getPassword(),
		]);
	}

	/**
	 * @param \Doctrine\DBAL\Connection $connection
	 * @return string
	 */
	private function getConnectionDsn(Connection $connection) {
		$connectionParams = $connection->getParams();

		$dsnParams = [];
		if (isset($connectionParams['host'])) {
			$dsnParams['host'] = $connectionParams['host'];
		}
		if (isset($connectionParams['port'])) {
			$dsnParams['port'] = $connectionParams['port'];
		}
		if (isset($connectionParams['dbname'])) {
			$dsnParams['dbname'] = $connectionParams['dbname'];
		}

		return 'pgsql:' . http_build_query($dsnParams, '', ';');
	}

}
