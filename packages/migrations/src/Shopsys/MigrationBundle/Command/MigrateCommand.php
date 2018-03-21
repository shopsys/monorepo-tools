<?php

namespace Shopsys\MigrationBundle\Command;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\OutputWriter;
use Doctrine\DBAL\Migrations\Tools\Console\Helper\ConfigurationHelper;
use Doctrine\ORM\EntityManagerInterface;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MigrateCommand extends Command
{
    const RETURN_CODE_OK = 0;
    const RETURN_CODE_ERROR = 1;

    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:migrations:migrate';

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLocator
     */
    private $migrationsLocator;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLocator $migrationsLocator
     */
    public function __construct(
        EntityManagerInterface $em,
        ContainerInterface $container,
        MigrationsLocator $migrationsLocator
    ) {
        $this->em = $em;
        $this->container = $container;
        $this->migrationsLocator = $migrationsLocator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription(
                'Execute all database migrations and check if database schema is satisfying ORM, all in one transaction.'
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $migrationsConfiguration = $this->createAndRegisterMigrationsConfiguration($output);

            foreach ($this->migrationsLocator->getMigrationsLocations() as $migrationsLocation) {
                $migrationsConfiguration->setMigrationsDirectory($migrationsLocation->getDirectory());
                $migrationsConfiguration->setMigrationsNamespace($migrationsLocation->getNamespace());

                $output->writeln('Installing migrations from ' . $migrationsLocation->getDirectory() . ' in namespace ' . $migrationsLocation->getNamespace());

                $this->em->transactional(function () use ($output) {
                    $this->executeDoctrineMigrateCommand($output);

                    $output->writeln('');
                });
            }

            $output->writeln('Migrations from all sources has been installed.');

            $this->executeCheckSchemaCommand($output);
        } catch (\Exception $ex) {
            $message = "Database migration process did not run properly. Last transaction was reverted.\n"
                . 'For more informations see the previous exception.';
            throw new \Shopsys\MigrationBundle\Command\Exception\MigrateCommandException($message, $ex);
        }
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return \Doctrine\DBAL\Migrations\Configuration\Configuration
     */
    private function createAndRegisterMigrationsConfiguration(OutputInterface $output)
    {
        $outputWriter = new OutputWriter(
            function ($message) use ($output) {
                return $output->writeln($message);
            }
        );

        $migrationsConfiguration = new Configuration($this->em->getConnection(), $outputWriter);
        $configurationHelper = new ConfigurationHelper($this->em->getConnection(), $migrationsConfiguration);
        $this->getApplication()->getHelperSet()->set($configurationHelper, 'configuration');

        return $migrationsConfiguration;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function executeDoctrineMigrateCommand(OutputInterface $output)
    {
        $doctrineMigrateCommand = $this->getApplication()->find('doctrine:migrations:migrate');
        $arguments = [
            'command' => 'doctrine:migrations:migrate',
            '--allow-no-migration' => true,
        ];

        $input = new ArrayInput($arguments);
        $input->setInteractive(false);

        $exitCode = $doctrineMigrateCommand->run($input, $output);

        if ($exitCode !== 0) {
            $message = 'Doctrine migration command did not exit properly (exit code is ' . $exitCode . ').';
            throw new \Shopsys\MigrationBundle\Command\Exception\MigrateCommandException($message);
        }
    }

    private function executeCheckSchemaCommand(OutputInterface $output)
    {
        $checkSchemaCommand = $this->getApplication()->find('shopsys:migrations:check-schema');
        $arguments = [
            'command' => 'shopsys:migrations:check-schema',
        ];
        $input = new ArrayInput($arguments);
        $input->setInteractive(false);

        $exitCode = $checkSchemaCommand->run($input, $output);

        if ($exitCode !== 0) {
            $message = 'Database schema check did not exit properly (exit code is ' . $exitCode . ').';
            throw new \Shopsys\MigrationBundle\Command\Exception\CheckSchemaCommandException($message);
        }
    }
}
