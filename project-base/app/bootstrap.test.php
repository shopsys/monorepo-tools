<?php

require_once __DIR__ . '/Bootstrap.php';
require_once __DIR__ . '/Environment.php';

use SS6\Bootstrap;
use SS6\Environment;

$bootstrap = new Bootstrap(false, Environment::ENVIRONMENT_TEST);
$bootstrap->run();
