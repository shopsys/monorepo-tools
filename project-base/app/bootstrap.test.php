<?php

require_once __DIR__ . '/Bootstrap.php';

use ND\Bootstrap;

$bootstrap = new Bootstrap(Bootstrap::ENVIROMENT_TEST);
$bootstrap->run();
 