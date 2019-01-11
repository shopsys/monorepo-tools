<?php

namespace Shopsys\FrameworkBundle\Component\Filesystem;

use League\Flysystem\FilesystemInterface;

interface FilesystemFactoryInterface
{
    /**
     * @return \League\Flysystem\FilesystemInterface
     */
    public function create(): FilesystemInterface;
}
