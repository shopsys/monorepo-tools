<?php

$originalConfig = file_exists(__DIR__ . '/../../vendor/shopsys/coding-standards/build/phpcs-fixer.php_cs') ?
    include __DIR__ . '/../../vendor/shopsys/coding-standards/build/phpcs-fixer.php_cs' : include __DIR__ . '/../vendor/shopsys/coding-standards/build/phpcs-fixer.php_cs';

$originalConfig->getFinder()
    ->exclude('_generated');

return $originalConfig;