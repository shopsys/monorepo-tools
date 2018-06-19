<?php

namespace Shopsys\MigrationBundle\Command;

use Doctrine\DBAL\Migrations\OutputWriter;
use Doctrine\DBAL\Migrations\Tools\Console\Helper\ConfigurationHelper;
use Doctrine\ORM\EntityManagerInterface;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\Configuration;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsFinder;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLock;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLock
     */
    private $migrationsLock;

    /**
     * @var \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsFinder
     */
    private $migrationsFinder;

    /**
     * @var \Shopsys\MigrationBundle\Component\Doctrine\Migrations\Configuration
     */
    private $migrationsConfiguration;

    /**
     * @required
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLock $migrationsLock
     * @param \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsFinder $migrationsFinder
     */
    public function autowireDependencies(
        EntityManagerInterface $em,
        MigrationsLock $migrationsLock,
        MigrationsFinder $migrationsFinder
    ) {
        $this->em = $em;
        $this->migrationsLock = $migrationsLock;
        $this->migrationsFinder = $migrationsFinder;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $outputWriter = new OutputWriter(
            function ($message) use ($output) {
                return $output->writeln($message);
            }
        );

        $this->migrationsConfiguration = new Configuration($this->migrationsLock, $this->em->getConnection(), $outputWriter, $this->migrationsFinder);
        $configurationHelper = new ConfigurationHelper($this->em->getConnection(), $this->migrationsConfiguration);
        $this->getApplication()->getHelperSet()->set($configurationHelper, 'configuration');
    }

    /**
     * @return \Shopsys\MigrationBundle\Component\Doctrine\Migrations\Configuration
     */
    protected function getMigrationsConfiguration()
    {
        return $this->migrationsConfiguration;
    }
}
