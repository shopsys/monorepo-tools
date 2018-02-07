<?php

namespace Shopsys\ShopBundle\Command;

use Doctrine\DBAL\DriverManager;
use Shopsys\ShopBundle\Component\System\PostgresqlLocaleMapper;
use Shopsys\ShopBundle\Component\System\System;
use Shopsys\ShopBundle\Model\Localization\Localization;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateDatabaseCommand extends ContainerAwareCommand
{
    /**
     * @var \Doctrine\DBAL\Connection|null
     */
    private $connection;

    protected function configure()
    {
        $this
            ->setName('shopsys:database:create')
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

        // We need to create the required collations in the newly created database in order
        // to have a list of locales we can rely on. Unfortunately, in PostgreSQL locales
        // are operating system dependent, which means that they can be different on each system.
        // See https://www.postgresql.org/docs/9.6/static/collation.html for more details.
        // Hopefully, this will be removed with the introduction of ICU collations
        // in PostgreSQL v10 (https://www.postgresql.org/docs/10/static/collation.html).
        $this->createSystemSpecificCollationsIfNotExist($symfonyStyleIo);

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
    }

    /**
     * @param \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyleIo
     */
    private function createSystemSpecificCollationsIfNotExist(SymfonyStyle $symfonyStyleIo)
    {
        $localization = $this->getContainer()->get(Localization::class);
        /* @var $localization \Shopsys\ShopBundle\Model\Localization\Localization */
        $system = $this->getContainer()->get(System::class);
        /* @var $system \Shopsys\ShopBundle\Component\System\System */
        $postgresqlLocaleMapper = $this->getContainer()->get(PostgresqlLocaleMapper::class);
        /* @var $postgresqlLocaleMapper \Shopsys\ShopBundle\Component\System\PostgresqlLocaleMapper */

        $missingLocaleExceptions = [];
        foreach ($localization->getAllDefinedCollations() as $collation) {
            try {
                if ($system->isWindows()) {
                    $systemSpecificLocaleName = $postgresqlLocaleMapper->getWindowsLocale($collation);
                } elseif ($system->isMac()) {
                    $systemSpecificLocaleName = $postgresqlLocaleMapper->getMacOsxLocale($collation);
                } else {
                    $systemSpecificLocaleName = $postgresqlLocaleMapper->getLinuxLocale($collation);
                }

                $this->createCollationIfNotExists($collation, $systemSpecificLocaleName);
            } catch (\Shopsys\ShopBundle\Command\Exception\MissingLocaleException $e) {
                $missingLocaleExceptions[] = $e;
            }
        }

        if (count($missingLocaleExceptions) > 0) {
            throw new \Shopsys\ShopBundle\Command\Exception\MissingLocaleAggregateException($missingLocaleExceptions);
        }

        $symfonyStyleIo->success('Collations are created');
    }

    /**
     * @param string $collation
     * @param string $locale
     */
    private function createCollationIfNotExists($collation, $locale)
    {
        $connection = $this->getConnection();

        // Collations are created in schema "pg_catalog" for two reasons:
        // - This is the schema where PostgreSQL creates collations during "initdb" command.
        // - It is easier to dump and import the database across different systems because
        //   we can dump only "public" schema which contains the data while not having
        //   collations that are operating system specific as part of the dump.
        $stmt = $connection->executeQuery(
            'SELECT 1
            FROM pg_collation
            WHERE collnamespace = (SELECT oid FROM pg_namespace WHERE nspname = \'pg_catalog\')
            AND collencoding = pg_char_to_encoding(\'UTF8\')
            AND collname = ?',
            [$collation]
        );

        if ($stmt->fetchColumn() === false) {
            try {
                $connection->exec(sprintf(
                    'CREATE COLLATION pg_catalog.%s (LOCALE=%s)',
                    $connection->quoteIdentifier($collation),
                    $connection->quoteIdentifier($locale)
                ));
            } catch (\Doctrine\DBAL\Exception\DriverException $e) {
                if (preg_match('/could not create locale/ui', $e->getMessage())) {
                    $e = new \Shopsys\ShopBundle\Command\Exception\MissingLocaleException($locale, $e);
                }

                throw $e;
            }
        }
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
        $doctrineRegistry = $this->getContainer()->get('doctrine');
        /* @var $doctrineRegistry \Symfony\Bridge\Doctrine\RegistryInterface */

        $defaultConnectionName = $doctrineRegistry->getDefaultConnectionName();

        return $doctrineRegistry->getConnection($defaultConnectionName);
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
