<?php

// Clean Symfony cache automatically before running tests with PHPUnit.
// Otherwise tests can fail because of old cached files which can be confusing.
// Inspired by: http://symfony.com/doc/3.3/testing/bootstrap.html

$binDir = __DIR__ . '/../bin';

passthru(sprintf('php "%s/console" cache:clear --env=test --no-warmup', $binDir), $exitCode);
if ($exitCode !== 0) {
    exit($exitCode);
}

require __DIR__ . '/autoload.php';
