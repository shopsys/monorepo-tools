<?php

namespace SS6\ShopBundle\Tests\Test\Codeception\Module;

use Codeception\Module\Db as BaseDb;
use SS6\ShopBundle\Component\Doctrine\DatabaseSchemaFacade;
use SS6\ShopBundle\Tests\Test\Codeception\Helper\SymfonyHelper;

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

	public function cleanup() {
		$symfonyHelper = $this->getModule(SymfonyHelper::class);
		/* @var $symfonyHelper \SS6\ShopBundle\Tests\Test\Codeception\Helper\SymfonyHelper */
		$databaseSchemaFacade = $symfonyHelper->grabServiceFromContainer(DatabaseSchemaFacade::class);
		/* @var $databaseSchemaFacade \SS6\ShopBundle\Component\Doctrine\DatabaseSchemaFacade */
		$databaseSchemaFacade->dropSchemaIfExists('public');
		$databaseSchemaFacade->createSchema('public');
	}

}
