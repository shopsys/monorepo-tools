<?php

require_once __DIR__ . '/../vendor/autoload.php';

$output = new Symfony\Component\Console\Output\ConsoleOutput();
$command = new SS6\ShopBundle\Command\ConfigVersionsCheckCommand($output);

$returnCode = $command->check();
if ($returnCode !== null) {
	exit($returnCode);
}