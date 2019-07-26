<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Test\Codeception\Module;

use Codeception\Module\Db as BaseDb;
use Shopsys\FrameworkBundle\Component\Doctrine\DatabaseSchemaFacade;
use Tests\ShopBundle\Test\Codeception\Helper\SymfonyHelper;

class Db extends BaseDb
{
    /**
     * Revert database to the original state
     */
    public function _afterSuite()
    {
        $this->cleanup();
        $this->_loadDump();
    }

    public function cleanup()
    {
        /** @var \Tests\ShopBundle\Test\Codeception\Helper\SymfonyHelper $symfonyHelper */
        $symfonyHelper = $this->getModule(SymfonyHelper::class);
        /** @var \Shopsys\FrameworkBundle\Component\Doctrine\DatabaseSchemaFacade $databaseSchemaFacade */
        $databaseSchemaFacade = $symfonyHelper->grabServiceFromContainer(DatabaseSchemaFacade::class);
        $databaseSchemaFacade->dropSchemaIfExists('public');
        $databaseSchemaFacade->createSchema('public');
    }
}
