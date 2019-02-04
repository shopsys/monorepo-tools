<?php

namespace Shopsys\FrameworkBundle\Component\Filesystem;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;

class MainFilesystemFactory implements FilesystemFactoryInterface
{
    /**
     * @var string
     */
    protected $projectDir;

    /**
     * @param string $projectDir
     */
    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    /**
     * @return \League\Flysystem\FilesystemInterface
     */
    public function create(): FilesystemInterface
    {
        $adapter = new Local($this->projectDir);

        return new Filesystem($adapter);
    }
}
