<?php

namespace Shopsys\FrameworkBundle\Command;

use Shopsys\FrameworkBundle\Component\Doctrine\DatabaseSchemaFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDatabaseSchemaCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:schema:create';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Doctrine\DatabaseSchemaFacade
     */
    private $databaseSchemaFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Doctrine\DatabaseSchemaFacade $databaseSchemaFacade
     */
    public function __construct(DatabaseSchemaFacade $databaseSchemaFacade)
    {
        $this->databaseSchemaFacade = $databaseSchemaFacade;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Create database public schema');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Initializing database schema');
        $this->databaseSchemaFacade->createSchema('public');
        $output->writeln('Database schema created successfully!');
    }
}
