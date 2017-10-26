<?php

use Symfony\Component\Filesystem\Filesystem;

// Clean Symfony cache automatically before running tests with PHPUnit.
// Otherwise tests can fail because of old cached files which can be confusing.
// Inspired by: http://symfony.com/doc/3.3/testing/bootstrap.html


function deleteCacheFolder()
{
    $cacheDir = __DIR__ . '/../var/cache';
    $filesystem = new Filesystem();
    $filesystem->remove($cacheDir);
}

deleteCacheFolder();

require __DIR__ . '/autoload.php';
