<?php

namespace ShopSys\MigrationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateMigrationCommand extends ContainerAwareCommand
{
    const RETURN_CODE_OK = 0;
    const RETURN_CODE_ERROR = 1;

    protected function configure()
    {
        $this
            ->setName('shopsys:migrations:generate')
            ->setDescription('Generate a new migration if need it');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $databaseSchemaFacade = $this->getContainer()->get('shopsys.migration.component.doctrine.database_schema_facade');
        /* @var $databaseSchemaFacade \ShopSys\MigrationBundle\Component\Doctrine\DatabaseSchemaFacade */

        $generateMigrationsService = $this->getContainer()->get('shopsys.migration.component.generator.generate_migrations_service');
        /* @var $generateMigrationsService \ShopSys\MigrationBundle\Component\Generator\GenerateMigrationsService */

        $output->writeln('Checking database schema...');

        $filteredSchemaDiffSqlCommands = $databaseSchemaFacade->getFilteredSchemaDiffSqlCommands();
        if (count($filteredSchemaDiffSqlCommands) === 0) {
            $output->writeln('<info>Database schema is satisfying ORM, no migrations was generated.</info>');

            return self::RETURN_CODE_OK;
        }

        $generatorResult = $generateMigrationsService->generate($filteredSchemaDiffSqlCommands);

        if ($generatorResult->hasError()) {
            $output->writeln('<error>Migration file "' . $generatorResult->getMigrationFilePath() . '" could not be saved.</error>');

            return self::RETURN_CODE_ERROR;
        }

        $output->writeln('<info>Database schema is not satisfying ORM, it was generated a new migration!</info>');
        $output->writeln(sprintf(
            '<info>Migration file "%s" was saved (%d B).</info>',
            $generatorResult->getMigrationFilePath(),
            $generatorResult->getWrittenBytes()
        ));

        return self::RETURN_CODE_OK;
    }
}
