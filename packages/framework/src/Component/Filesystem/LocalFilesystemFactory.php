<?php

namespace Shopsys\FrameworkBundle\Component\Filesystem;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;

class LocalFilesystemFactory implements FilesystemFactoryInterface
{
    /**
     * @return \League\Flysystem\FilesystemInterface
     */
    public function create(): FilesystemInterface
    {
        $adapter = new Local('/');

        return new Filesystem($adapter);
    }
}
