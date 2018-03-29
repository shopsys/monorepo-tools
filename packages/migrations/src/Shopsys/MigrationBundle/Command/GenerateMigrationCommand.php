<?php

namespace Shopsys\MigrationBundle\Command;

use Shopsys\MigrationBundle\Component\Doctrine\DatabaseSchemaFacade;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLocator;
use Shopsys\MigrationBundle\Component\Generator\GenerateMigrationsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class GenerateMigrationCommand extends Command
{
    const RETURN_CODE_OK = 0;
    const RETURN_CODE_ERROR = 1;

    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:migrations:generate';

    /**
     * @var \Shopsys\MigrationBundle\Component\Doctrine\DatabaseSchemaFacade
     */
    private $databaseSchemaFacade;

    /**
     * @var \Shopsys\MigrationBundle\Component\Generator\GenerateMigrationsService
     */
    private $generateMigrationsService;

    /**
     * @var \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLocator
     */
    private $migrationsLocator;

    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface
     */
    private $kernel;

    /**
     * @var string
     */
    private $vendorDirectoryPath;

    /**
     * @param string $vendorDirectoryPath
     * @param \Shopsys\MigrationBundle\Component\Doctrine\DatabaseSchemaFacade $databaseSchemaFacade
     * @param \Shopsys\MigrationBundle\Component\Generator\GenerateMigrationsService $generateMigrationsService
     * @param \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLocator $migrationsLocator
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     */
    public function __construct(
        $vendorDirectoryPath,
        DatabaseSchemaFacade $databaseSchemaFacade,
        GenerateMigrationsService $generateMigrationsService,
        MigrationsLocator $migrationsLocator,
        KernelInterface $kernel
    ) {
        $this->databaseSchemaFacade = $databaseSchemaFacade;
        $this->generateMigrationsService = $generateMigrationsService;
        $this->migrationsLocator = $migrationsLocator;
        $this->kernel = $kernel;
        $this->vendorDirectoryPath = $vendorDirectoryPath;
        parent::__construct();
    }

    protected function configure()
    {
        $this
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

        $io = new SymfonyStyle($input, $output);

        $migrationsLocation = $this->chooseMigrationLocation($io);

        $generatorResult = $this->generateMigrationsService->generate(
            $filteredSchemaDiffSqlCommands,
            $migrationsLocation
        );

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

    /**
     * @param \Symfony\Component\Console\Style\SymfonyStyle $io
     * @return \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLocation
     */
    private function chooseMigrationLocation(SymfonyStyle $io)
    {
        $bundles = $this->getAllBundleNamesExceptVendor();

        if (count($bundles) > 1) {
            $chosenBundle = $io->choice(
                'There is more than one bundle available as the destination of generated migrations. Which bundle would you like to choose?',
                $bundles
            );
        } else {
            $chosenBundle = reset($bundles);
        }

        return $this->getMigrationLocation($this->kernel->getBundle($chosenBundle));
    }

    /**
     * @return string[]
     */
    private function getAllBundleNamesExceptVendor()
    {
        $bundles = [];
        foreach ($this->kernel->getBundles() as $bundle) {
            if (strpos(realpath($bundle->getPath()), realpath($this->vendorDirectoryPath)) !== 0) {
                $bundles[] = $bundle->getName();
            }
        }
        return $bundles;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle
     * @return \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLocation
     */
    private function getMigrationLocation(BundleInterface $bundle)
    {
        return $this->migrationsLocator->createMigrationsLocation($bundle);
    }
}
