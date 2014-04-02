<?php 

use ND\Bootstrap;

require_once __DIR__ . '/../app/Bootstrap.php';

$bootstrap = new Bootstrap(Bootstrap::ENVIROMENT_DEVELOPMENT);
$bootstrap->run();
