<?php 

// if you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
umask(0002);

use SS6\Bootstrap;

require_once __DIR__ . '/../app/Bootstrap.php';

$bootstrap = new Bootstrap();
$bootstrap->run();
