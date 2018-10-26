<?php

namespace Shopsys\FrameworkBundle\Component\Environment;

class EnvironmentFileSetting
{
    const FILE_NAMES_BY_ENVIRONMENT = [
        EnvironmentType::DEVELOPMENT => 'DEVELOPMENT',
        EnvironmentType::PRODUCTION => 'PRODUCTION',
        EnvironmentType::TEST => 'TEST',
    ];

    const ENVIRONMENTS_CONSOLE = [EnvironmentType::DEVELOPMENT, EnvironmentType::PRODUCTION];
    const ENVIRONMENTS_DEFAULT = [EnvironmentType::TEST, EnvironmentType::DEVELOPMENT, EnvironmentType::PRODUCTION];

    /**
     * @var string
     */
    private $environmentFileDirectory;

    /**
     * @param string $environmentFileDirectory
     */
    public function __construct(string $environmentFileDirectory)
    {
        $this->environmentFileDirectory = $environmentFileDirectory;
    }

    /**
     * @param bool $console
     * @return string
     */
    public function getEnvironment(bool $console): string
    {
        $environments = $console ? self::ENVIRONMENTS_CONSOLE : self::ENVIRONMENTS_DEFAULT;
        foreach ($environments as $environment) {
            if (is_file($this->getEnvironmentFilePath($environment))) {
                return $environment;
            }
        }

        return EnvironmentType::PRODUCTION;
    }

    /**
     * @return bool
     */
    public function isAnyEnvironmentSet(): bool
    {
        foreach (EnvironmentType::ALL as $environment) {
            if (is_file($this->getEnvironmentFilePath($environment))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $environment
     */
    public function createFileForEnvironment(string $environment): void
    {
        touch($this->getEnvironmentFilePath($environment));
    }

    public function removeFilesForAllEnvironments(): void
    {
        foreach (EnvironmentType::ALL as $environment) {
            $environmentFilePath = $this->getEnvironmentFilePath($environment);

            if (is_file($environmentFilePath)) {
                unlink($environmentFilePath);
            }
        }
    }

    /**
     * @param string $environment
     * @return string
     */
    private function getEnvironmentFilePath(string $environment): string
    {
        return $this->environmentFileDirectory . '/' . self::FILE_NAMES_BY_ENVIRONMENT[$environment];
    }
}
