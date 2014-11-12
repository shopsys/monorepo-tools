<?php

namespace SS6;

use SS6\Environment;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Debug\ErrorHandler;

require_once __DIR__ . '/../app/bootstrap.php.cache';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';
require_once __DIR__ . '/../app/Environment.php';

class Bootstrap {
	private $environment;
	private $console;

	public function __construct($console = false, $environment = null) {
		if ($environment === null) {
			$this->environment = Environment::getEnvironment();
		} else {
			$this->environment = $environment;
		}
		$this->console = (bool)$console;
	}

	public function run() {

		if ($this->isDebug()) {
			Debug::enable();
		} else {
			ErrorHandler::register();
		}

		$kernel = new \AppKernel($this->environment, $this->isDebug());
		$kernel->addConfig($this->getConfigs());
		$kernel->loadClassCache();
		if ($this->console) {
			$input = new ArgvInput();
			$application = new Application($kernel);
			$application->run($input);
		} else {
			if ($this->environment === Environment::ENVIRONMENT_TEST) {
				$kernel->boot();
			} else {
				$this->initDoctrine();

				$request = Request::createFromGlobals();
				$response = $kernel->handle($request);
				$response->send();
				$kernel->terminate($request, $response);
			}
		}
	}

	private function isDebug() {
		return in_array($this->environment, array(Environment::ENVIRONMENT_DEVELOPMENT, Environment::ENVIRONMENT_TEST));
	}

	private function initDoctrine() {
		if ($this->environment === Environment::ENVIRONMENT_DEVELOPMENT) {
			$dirs = array(__DIR__ . '/../vendor/doctrine/orm/lib/');
			\Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace('Doctrine\ORM', $dirs);
		}
	}

	private function getConfigs() {
		$configs = array(
			__DIR__ . '/config/parameters_common.yml',
			__DIR__ . '/config/parameters.yml',
			__DIR__ . '/config/config.yml',
			__DIR__ . '/config/security.yml',
		);
		switch ($this->environment) {
			case Environment::ENVIRONMENT_DEVELOPMENT:
				$configs[] = __DIR__ . '/config/config_dev.yml';
				break;
			case Environment::ENVIRONMENT_PRODUCTION:
				$configs[] = __DIR__ . '/config/config_prod.yml';
				break;
			case Environment::ENVIRONMENT_TEST:
				$configs[] = __DIR__ . '/config/parameters_test.yml';
				$configs[] = __DIR__ . '/config/config_test.yml';
				break;
		}

		return $configs;
	}
}
