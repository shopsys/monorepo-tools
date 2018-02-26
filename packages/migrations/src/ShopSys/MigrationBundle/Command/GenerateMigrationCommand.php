<?php

namespace ShopSys\MigrationBundle\Command;

use ShopSys\MigrationBundle\Component\Doctrine\DatabaseSchemaFacade;
use ShopSys\MigrationBundle\Component\Generator\GenerateMigrationsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateMigrationCommand extends Command
{
    const RETURN_CODE_OK = 0;
    const RETURN_CODE_ERROR = 1;

    /**
     * @var \ShopSys\MigrationBundle\Component\Doctrine\DatabaseSchemaFacade
     */
    private $databaseSchemaFacade;

    /**
     * @var \ShopSys\MigrationBundle\Component\Generator\GenerateMigrationsService
     */
    private $generateMigrationsService;

    /**
     * @param \ShopSys\MigrationBundle\Component\Doctrine\DatabaseSchemaFacade $databaseSchemaFacade
     * @param \ShopSys\MigrationBundle\Component\Generator\GenerateMigrationsService $generateMigrationsService
     */
    public function __construct(
        DatabaseSchemaFacade $databaseSchemaFacade,
        GenerateMigrationsService $generateMigrationsService
    ) {
        $this->databaseSchemaFacade = $databaseSchemaFacade;
        $this->generateMigrationsService = $generateMigrationsService;

        parent::__construct();
    }

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
        $output->writeln('Checking database schema...');

        $filteredSchemaDiffSqlCommands = $this->databaseSchemaFacade->getFilteredSchemaDiffSqlCommands();
        if (count($filteredSchemaDiffSqlCommands) === 0) {
            $output->writeln('<info>Database schema is satisfying ORM, no migrations was generated.</info>');

            return self::RETURN_CODE_OK;
        }

        $generatorResult = $this->generateMigrationsService->generate($filteredSchemaDiffSqlCommands);

        if ($generatorResult->hasError()) {
            $output->writeln('<error>Migration file "' . $generatorResult->getMigrationFilePath() . '" could not be saved.</error>');

            return self::RETURN_CODE_ERROR;
        }

        $output->writeln('<info>Database schema is not satisfying ORM, a new migration was generated!</info>');
        $output->writeln(sprintf(
            '<info>Migration file "%s" was saved (%d B).</info>',
            $generatorResult->getMigrationFilePath(),
            $generatorResult->getWrittenBytes()
        ));

        return self::RETURN_CODE_OK;
    }
}
