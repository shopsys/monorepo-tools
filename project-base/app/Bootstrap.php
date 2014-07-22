<?php

namespace SS6;

use SS6\ShopBundle\Component\Environment;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Debug\Debug;

require_once __DIR__ . '/../app/bootstrap.php.cache';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

class Bootstrap {
	const ENVIRONMENT_PRODUCTION = 'prod';
	const ENVIRONMENT_DEVELOPMENT = 'dev';
	const ENVIRONMENT_TEST = 'test';
	
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
		}

		$kernel = new \AppKernel($this->environment, $this->isDebug());
		$kernel->addConfig($this->getConfigs());
		$kernel->loadClassCache();
		if ($this->console) {
			$input = new ArgvInput();
			$application = new Application($kernel);
			$application->run($input);
		} else {
			if ($this->environment === self::ENVIRONMENT_TEST) {
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
		return in_array($this->environment, array(self::ENVIRONMENT_DEVELOPMENT, self::ENVIRONMENT_TEST));
	}
	
	private function initDoctrine() {
		if ($this->environment === self::ENVIRONMENT_DEVELOPMENT) {
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
			case self::ENVIRONMENT_DEVELOPMENT:
				$configs[] = __DIR__ . '/config/config_dev.yml';
				break;
			case self::ENVIRONMENT_PRODUCTION:
				$configs[] = __DIR__ . '/config/config_prod.yml';
				break;
			case self::ENVIRONMENT_TEST:
				$configs[] = __DIR__ . '/config/parameters_test.yml';
				$configs[] = __DIR__ . '/config/config_dev.yml';
				$configs[] = __DIR__ . '/config/config_test.yml';
				break;
		}
		
		return $configs;
	}
}