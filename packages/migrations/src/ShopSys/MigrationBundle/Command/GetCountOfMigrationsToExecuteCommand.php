<?php

namespace ShopSys\MigrationBundle\Command;

use Doctrine\Bundle\MigrationsBundle\Command\DoctrineCommand;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetCountOfMigrationsToExecuteCommand extends ContainerAwareCommand
{

    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:migrations:count';

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Get count of migrations to execute.');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrationsConfiguration = new Configuration($this->em->getConnection());

        DoctrineCommand::configureMigrations($this->getContainer(), $migrationsConfiguration);

        $migratedVersions = $migrationsConfiguration->getMigratedVersions();
        $availableVersions = $migrationsConfiguration->getAvailableVersions();

        $newMigrationsCount = count(array_diff($availableVersions, $migratedVersions));

        $output->writeln('Count of migrations to execute: ' . $newMigrationsCount);
    }
}
