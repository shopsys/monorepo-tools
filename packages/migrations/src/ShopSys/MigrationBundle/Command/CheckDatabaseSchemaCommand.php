<?php

namespace ShopSys\MigrationBundle\Command;

use ShopSys\MigrationBundle\Component\Doctrine\DatabaseSchemaFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckDatabaseSchemaCommand extends Command
{
    const RETURN_CODE_OK = 0;
    const RETURN_CODE_ERROR = 1;

    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:migrations:check-schema';

    /**
     * @var \ShopSys\MigrationBundle\Component\Doctrine\DatabaseSchemaFacade
     */
    private $databaseSchemaFacade;

    /**
     * @param \ShopSys\MigrationBundle\Component\Doctrine\DatabaseSchemaFacade $databaseSchemaFacade
     */
    public function __construct(DatabaseSchemaFacade $databaseSchemaFacade)
    {
        $this->databaseSchemaFacade = $databaseSchemaFacade;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Check if database schema is satisfying ORM');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Checking database schema...');

        $filteredSchemaDiffSqlCommands = $this->databaseSchemaFacade->getFilteredSchemaDiffSqlCommands();
        if (count($filteredSchemaDiffSqlCommands) === 0) {
            $output->writeln('<info>Database schema is satisfying ORM.</info>');
        } else {
            $output->writeln('<error>Database schema is not satisfying ORM!</error>');
            $output->writeln('<error>Following SQL commands should fix the problem (revise them before!):</error>');
            $output->writeln('');
            foreach ($filteredSchemaDiffSqlCommands as $sqlCommand) {
                $output->writeln('<fg=red>' . $sqlCommand . ';</fg=red>');
            }
            $output->writeln('<info>TIP: you can use shopsys:migrations:generate</info>');
            $output->writeln('');

            return self::RETURN_CODE_ERROR;
        }

        return self::RETURN_CODE_OK;
    }
}
