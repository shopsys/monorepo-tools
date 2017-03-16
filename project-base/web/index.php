<?php

// if you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
umask(0002);

use Shopsys\Bootstrap;

if (file_exists(__DIR__ . '/../MAINTENANCE')) {
    require __DIR__ . '/../app/maintenance.php';
} else {
    require_once __DIR__ . '/../app/autoload.php';

    $bootstrap = new Bootstrap();
    $bootstrap->run();
}
