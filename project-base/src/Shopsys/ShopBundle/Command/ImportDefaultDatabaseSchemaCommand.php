<?php

namespace Shopsys\ShopBundle\Command;

use Shopsys\ShopBundle\Component\Doctrine\DatabaseSchemaFacade;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportDefaultDatabaseSchemaCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
            ->setName('shopsys:schema:import-default')
            ->setDescription('Import database default schema');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $databaseSchemaFacade = $this->getContainer()->get(DatabaseSchemaFacade::class);
        /* @var $databaseSchemaFacade \Shopsys\ShopBundle\Component\Doctrine\DatabaseSchemaFacade */

        $output->writeln('Importing default database schema...');
        $databaseSchemaFacade->importDefaultSchema();
        $output->writeln('Default database schema imported successfully!');
    }

}
