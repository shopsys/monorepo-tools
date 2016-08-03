<?php

namespace SS6\ShopBundle\Component\Error;

use SS6\ShopBundle\Component\Cron\CronModuleInterface;
use SS6\ShopBundle\Component\Error\ErrorPagesFacade;
use Symfony\Bridge\Monolog\Logger;

class ErrorPageCronModule implements CronModuleInterface {

	/**
	 * @var \SS6\ShopBundle\Component\Error\ErrorPagesFacade
	 */
	private $errorPagesFacade;

	public function __construct(ErrorPagesFacade $errorPagesFacade) {
		$this->errorPagesFacade = $errorPagesFacade;
	}

	/**
	 * @inheritdoc
	 */
	public function setLogger(Logger $logger) {

	}

	public function run() {
		$this->errorPagesFacade->generateAllErrorPagesForProduction();
	}

}
