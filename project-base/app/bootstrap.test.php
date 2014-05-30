<?php

require_once __DIR__ . '/Bootstrap.php';

use SS6\Bootstrap;

$bootstrap = new Bootstrap(false, Bootstrap::ENVIROMENT_TEST);
$bootstrap->run();
 