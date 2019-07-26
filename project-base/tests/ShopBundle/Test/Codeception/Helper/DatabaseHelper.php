<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Test\Codeception\Helper;

use Codeception\Module;
use Doctrine\DBAL\Connection;
use Tests\ShopBundle\Test\Codeception\Module\Db;

class DatabaseHelper extends Module
{
    /**
     * {@inheritDoc}
     */
    public function _initialize()
    {
        /** @var \Tests\ShopBundle\Test\Codeception\Module\Db $dbModule */
        $dbModule = $this->getModule(Db::class);
        /** @var \Tests\ShopBundle\Test\Codeception\Helper\SymfonyHelper $symfonyHelper */
        $symfonyHelper = $this->getModule(SymfonyHelper::class);
        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $symfonyHelper->grabServiceFromContainer('doctrine.dbal.default_connection');

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
    private function getConnectionDsn(Connection $connection)
    {
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
