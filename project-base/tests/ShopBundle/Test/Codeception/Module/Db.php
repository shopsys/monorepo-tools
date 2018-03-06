<?php

namespace Tests\ShopBundle\Test\Codeception\Module;

use Codeception\Module\Db as BaseDb;
use Shopsys\FrameworkBundle\Component\Doctrine\DatabaseSchemaFacade;
use Tests\ShopBundle\Test\Codeception\Helper\SymfonyHelper;

class Db extends BaseDb
{
    // @codingStandardsIgnoreStart
    /**
     * Revert database to the original state
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function _afterSuite()
    {
        // @codingStandardsIgnoreEnd
        $this->cleanup();
        $this->_loadDump();
    }

    public function cleanup()
    {
        $symfonyHelper = $this->getModule(SymfonyHelper::class);
        /* @var $symfonyHelper \Tests\ShopBundle\Test\Codeception\Helper\SymfonyHelper */
        $databaseSchemaFacade = $symfonyHelper->grabServiceFromContainer(DatabaseSchemaFacade::class);
        /* @var $databaseSchemaFacade \Shopsys\FrameworkBundle\Component\Doctrine\DatabaseSchemaFacade */
        $databaseSchemaFacade->dropSchemaIfExists('public');
        $databaseSchemaFacade->createSchema('public');
    }
}
