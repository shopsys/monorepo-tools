<?php

namespace Shopsys\ShopBundle\Command;

use Shopsys\ShopBundle\Component\Doctrine\DatabaseSchemaFacade;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DropDatabaseSchemaCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('shopsys:schema:drop')
            ->setDescription('Drop database public schema');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $databaseSchemaFacade = $this->getContainer()->get(DatabaseSchemaFacade::class);
        /* @var $databaseSchemaFacade \Shopsys\ShopBundle\Component\Doctrine\DatabaseSchemaFacade */

        $output->writeln('Dropping database schema...');
        $databaseSchemaFacade->dropSchemaIfExists('public');
        $output->writeln('Database schema dropped successfully!');
    }
}
