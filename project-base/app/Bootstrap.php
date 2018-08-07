<?php

namespace Shopsys;

use AppKernel;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Shopsys\FrameworkBundle\Component\Environment\EnvironmentType;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/autoload.php';

setlocale(LC_CTYPE, 'en_US.utf8');

class Bootstrap
{
    private $environment;

    private $console;

    public function __construct($console = false, $environment = null)
    {
        if ($environment === null) {
            $this->environment = Environment::getEnvironment($console);
        } else {
            $this->environment = $environment;
        }
        $this->console = (bool)$console;
    }

    public function run()
    {
        if ($this->environment !== EnvironmentType::DEVELOPMENT) {
            // Speed-up loading in production using bootstrap file that combines multiple PHP files to reduce disk IO.
            // See http://symfony.com/doc/3.0/performance.html#use-bootstrap-files
            include_once __DIR__ . '/../var/bootstrap.php.cache';
        }

        $this->configurePhp();

        if (EnvironmentType::isDebug($this->environment)) {
            Debug::enable();
        } else {
            ErrorHandler::register();
        }

        $kernel = new AppKernel($this->environment, EnvironmentType::isDebug($this->environment));
        Request::setTrustedProxies(['127.0.0.1'], Request::HEADER_X_FORWARDED_ALL);
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

    private function configurePhp()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 0);
    }

    private function initDoctrine()
    {
        if ($this->environment === EnvironmentType::DEVELOPMENT) {
            $dirs = [__DIR__ . '/../vendor/doctrine/orm/lib/'];
            AnnotationRegistry::registerAutoloadNamespace('Doctrine\ORM', $dirs);
        }
    }
}
