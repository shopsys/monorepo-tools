<?php

namespace Shopsys\FrameworkBundle\Command;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateDatabaseCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:database:create';

    /**
     * @var \Doctrine\DBAL\Connection|null
     */
    private $connection;

    /**
     * @var \Doctrine\Common\Persistence\ManagerRegistry
     */
    private $doctrineRegistry;

    /**
     * @param \Doctrine\Common\Persistence\ManagerRegistry $managerRegistry
     */
    public function __construct(
        ManagerRegistry $managerRegistry
    ) {
        $this->doctrineRegistry = $managerRegistry;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates database with required db extensions');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $symfonyStyleIo = new SymfonyStyle($input, $output);

        $this->switchConnectionToSuperuser($symfonyStyleIo);

        $this->createDatabaseIfNotExists($symfonyStyleIo);
        $this->createExtensionsIfNotExist($symfonyStyleIo);

        return 0;
    }

    /**
     * @param \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyleIo
     */
    private function createDatabaseIfNotExists(SymfonyStyle $symfonyStyleIo)
    {
        $defaultConnection = $this->getDefaultConnection();

        $params = $defaultConnection->getParams();
        $databaseName = $params['dbname'];
        $databaseUser = $params['user'];

        $databaselessConnection = $this->createDatabaselessConnection();

        if (in_array($databaseName, $databaselessConnection->getSchemaManager()->listDatabases(), true)) {
            $symfonyStyleIo->note(sprintf('Database "%s" already exists', $databaseName));
        } else {
            $databaselessConnection->exec(sprintf(
                'CREATE DATABASE %s WITH OWNER = %s',
                $databaselessConnection->quoteIdentifier($databaseName),
                $databaselessConnection->quoteIdentifier($databaseUser)
            ));

            $this->getConnection()->exec(sprintf(
                'ALTER SCHEMA public OWNER TO %s',
                $databaselessConnection->quoteIdentifier($databaseUser)
            ));

            $symfonyStyleIo->success(sprintf('Database "%s" created', $databaseName));
        }
    }

    /**
     * @param \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyleIo
     */
    private function createExtensionsIfNotExist(SymfonyStyle $symfonyStyleIo)
    {
        // Extensions are created in schema "pg_catalog" in order to be able to DROP
        // schema "public" without dropping the extension.
        // We do not want to DROP the extension because it can only be created with
        // "superuser" role that normal DB user does not have.
        $this->getConnection()->exec('CREATE EXTENSION IF NOT EXISTS unaccent WITH SCHEMA pg_catalog');
        $symfonyStyleIo->success('Extension unaccent is created');
        $this->getConnection()->exec('CREATE EXTENSION IF NOT EXISTS "uuid-ossp" WITH SCHEMA pg_catalog');
        $symfonyStyleIo->success('Extension "uuid-ossp" is created');
    }

    /**
     * @param \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyleIo
     */
    private function switchConnectionToSuperuser(SymfonyStyle $symfonyStyleIo)
    {
        if (!$this->isConnectedAsSuperuser()) {
            $symfonyStyleIo->note('Current database user does not have a superuser permission');

            $params = $this->getConnection()->getParams();

            $userNameQuestion = new Question('Enter superuser name');
            $params['user'] = $symfonyStyleIo->askQuestion($userNameQuestion);

            $passwordQuestion = new Question('Enter superuser password');
            $passwordQuestion->setHidden(true);
            $passwordQuestion->setHiddenFallback(false);
            $params['password'] = $symfonyStyleIo->askQuestion($passwordQuestion);

            $this->connection = DriverManager::getConnection($params);
        } else {
            $symfonyStyleIo->caution(
                'Your database connection configuration contains superadmin credentials. This is not safe for '
                    . 'production use. We strongly recommend using non-superuser credentials for security reasons.'
            );
        }
    }

    /**
     * @return bool
     */
    private function isConnectedAsSuperuser()
    {
        $stmt = $this->createDatabaselessConnection()
            ->executeQuery('SELECT rolsuper FROM pg_roles WHERE rolname = current_user');

        return $stmt->fetchColumn();
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    private function getDefaultConnection()
    {
        $defaultConnectionName = $this->doctrineRegistry->getDefaultConnectionName();

        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $this->doctrineRegistry->getConnection($defaultConnectionName);
        return $connection;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    private function getConnection()
    {
        if ($this->connection === null) {
            $this->connection = $this->getDefaultConnection();
        }

        return $this->connection;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    private function createDatabaselessConnection()
    {
        $connection = $this->getConnection();

        $params = $connection->getParams();

        // remove "dbname" param so that doctrine does not try to connect to the database that does not exist yet
        unset($params['dbname']);

        return DriverManager::getConnection($params);
    }
}
