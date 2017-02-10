<?php

namespace Shopsys;

use Shopsys\Environment;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Debug\ErrorHandler;

require_once __DIR__ . '/../app/bootstrap.php.cache';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';
require_once __DIR__ . '/../app/Environment.php';

setlocale(LC_CTYPE, 'en_US.utf8');

class Bootstrap
{
    private $environment;
    private $console;

    public function __construct($console = false, $environment = null) {
        if ($environment === null) {
            $this->environment = Environment::getEnvironment($console);
        } else {
            $this->environment = $environment;
        }
        $this->console = (bool)$console;
    }

    public function run() {
        $this->configurePhp();

        if (Environment::isEnvironmentDebug($this->environment)) {
            Debug::enable();
        } else {
            ErrorHandler::register();
        }

        $kernel = new \AppKernel($this->environment, Environment::isEnvironmentDebug($this->environment));
        $kernel->loadClassCache();
        if ($this->console) {
            $input = new ArgvInput();
            $output = new ConsoleOutput();
            $output->getErrorOutput()->setVerbosity(ConsoleOutput::VERBOSITY_VERBOSE);

            $application = new Application($kernel);
            $application->run($input, $output);
        } else {
            $this->initDoctrine();

            $request = Request::createFromGlobals();
            $response = $kernel->handle($request);
            $response->send();
            $kernel->terminate($request, $response);
        }
    }

    private function configurePhp() {
        error_reporting(E_ALL);
        ini_set('display_errors', 0);
    }

    private function initDoctrine() {
        if ($this->environment === Environment::ENVIRONMENT_DEVELOPMENT) {
            $dirs = array(__DIR__ . '/../vendor/doctrine/orm/lib/');
            \Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace('Doctrine\ORM', $dirs);
        }
    }
}
