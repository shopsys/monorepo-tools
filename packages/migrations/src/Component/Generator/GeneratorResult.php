<?php

namespace Shopsys\MigrationBundle\Component\Generator;

class GeneratorResult
{
    /**
     * @var string
     */
    private $migrationFilePath;

    /**
     * @var int|false
     */
    private $writtenBytes;

    /**
     * @param string $migrationFilePath
     * @param int|false $writtenBytes
     */
    public function __construct($migrationFilePath, $writtenBytes)
    {
        $this->migrationFilePath = $migrationFilePath;
        $this->writtenBytes = $writtenBytes;
    }

    /**
     * @return string
     */
    public function getMigrationFilePath()
    {
        return $this->migrationFilePath;
    }

    /**
     * @return false|int
     */
    public function getWrittenBytes()
    {
        return $this->writtenBytes;
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return $this->writtenBytes === false || $this->writtenBytes === 0;
    }
}
