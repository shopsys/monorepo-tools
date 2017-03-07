<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require __DIR__ . '/../vendor/autoload.php';
/* @var $loader \Composer\Autoload\ClassLoader */

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $loader;
