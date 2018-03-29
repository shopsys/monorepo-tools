<?php

namespace Shopsys\MigrationBundle\Component\Generator;

use Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLocation;
use SqlFormatter;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Templating\EngineInterface;

class GenerateMigrationsService
{
    const LINE_LENGTH_LIMIT = 100;
    const HIGHLIGHT_OFF = false;
    const INDENT_CHARACTERS = '    ';
    const INDENT_TABULATOR_COUNT = 3;

    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    private $twigEngine;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @param \Symfony\Component\Templating\EngineInterface $twigEngine
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(
        EngineInterface $twigEngine,
        Filesystem $filesystem
    ) {
        $this->twigEngine = $twigEngine;
        $this->filesystem = $filesystem;
    }

    /**
     * @param array $sqlCommands
     * @param \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLocation $migrationsLocation
     * @return \Shopsys\MigrationBundle\Component\Generator\GeneratorResult
     */
    public function generate(array $sqlCommands, MigrationsLocation $migrationsLocation)
    {
        $this->createMigrationLocationDirectoryIfNotExists($migrationsLocation);
        $formattedSqlCommands = $this->formatSqlCommandsIfLengthOverflow($sqlCommands);
        $escapedFormattedSqlCommands = $this->escapeSqlCommands($formattedSqlCommands);
        $migrationClassName = 'Version' . date('YmdHis');
        $migrationFileRawData = $this->twigEngine->render('@ShopsysMigration/Migration/migration.php.twig', [
            'sqlCommands' => $escapedFormattedSqlCommands,
            'migrationClassName' => $migrationClassName,
            'namespace' => $migrationsLocation->getNamespace(),
        ]);

        $migrationFilePath = $migrationsLocation->getDirectory() . '/' . $migrationClassName . '.php';
        $writtenBytes = file_put_contents($migrationFilePath, $migrationFileRawData);

        return new GeneratorResult($migrationFilePath, $writtenBytes);
    }

    /**
     * @param string[] $filteredSchemaDiffSqlCommands
     * @return string[]
     */
    private function formatSqlCommandsIfLengthOverflow(array $filteredSchemaDiffSqlCommands)
    {
        $formattedSqlCommands = [];
        foreach ($filteredSchemaDiffSqlCommands as $key => $filteredSchemaDiffSqlCommand) {
            if (strlen($filteredSchemaDiffSqlCommand) > self::LINE_LENGTH_LIMIT) {
                $formattedSqlCommands[] = $this->formatSqlCommand($filteredSchemaDiffSqlCommand);
            } else {
                $formattedSqlCommands[] = $filteredSchemaDiffSqlCommand;
            }
        }

        return $formattedSqlCommands;
    }

    /**
     * @param string $filteredSchemaDiffSqlCommand
     * @return string
     */
    private function formatSqlCommand($filteredSchemaDiffSqlCommand)
    {
        $formattedQuery = $this->formatSqlQueryWithTabs($filteredSchemaDiffSqlCommand);
        $formattedQueryLines = array_map('rtrim', explode("\n", $formattedQuery));

        return "\n" . implode("\n", $this->indentSqlCommandLines($formattedQueryLines));
    }

    /**
     * @param string $query
     * @return string
     */
    private function formatSqlQueryWithTabs($query)
    {
        $previousTab = SqlFormatter::$tab;
        SqlFormatter::$tab = self::INDENT_CHARACTERS;

        $formattedQuery = SqlFormatter::format($query, self::HIGHLIGHT_OFF);

        SqlFormatter::$tab = $previousTab;

        return $formattedQuery;
    }

    /**
     * @param string[] $queryLines
     * @return string[]
     */
    private function indentSqlCommandLines(array $queryLines)
    {
        return array_map(function ($queryLine) {
            return str_repeat(self::INDENT_CHARACTERS, self::INDENT_TABULATOR_COUNT) . $queryLine;
        }, $queryLines);
    }

    /**
     * @param string[] $sqlCommands
     * @return string[]
     */
    private function escapeSqlCommands(array $sqlCommands)
    {
        return array_map(function ($sqlCommand) {
            return str_replace('\'', "\\'", $sqlCommand);
        }, $sqlCommands);
    }

    /**
     * @param \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLocation $migrationLocation
     */
    private function createMigrationLocationDirectoryIfNotExists(MigrationsLocation $migrationLocation)
    {
        if (!$this->filesystem->exists($migrationLocation->getDirectory())) {
            $this->filesystem->mkdir($migrationLocation->getDirectory());
        }
    }
}
