<?php

namespace Shopsys\ShopBundle\Tests\Test\Codeception\Helper;

use AppKernel;
use Codeception\Configuration;
use Codeception\Module;
use Codeception\TestInterface;
use Shopsys\Environment;

class SymfonyHelper extends Module
{

    /**
     * @var \Symfony\Component\HttpKernel\Kernel
     */
    private $kernel;

    // @codingStandardsIgnoreStart
    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function _initialize() {
    // @codingStandardsIgnoreEnd
        $projectDir = Configuration::projectDir();

        require_once $projectDir . '/../app/bootstrap.php.cache';
        require_once $projectDir . '/../vendor/autoload.php';
        require_once $projectDir . '/../app/AppKernel.php';
        require_once $projectDir . '/../app/Environment.php';

        $this->kernel = new AppKernel(Environment::ENVIRONMENT_TEST, true);
        $this->kernel->boot();
    }

    // @codingStandardsIgnoreStart
    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function _before(TestInterface $test) {
    // @codingStandardsIgnoreEnd
        $this->kernel->boot();
    }

    /**
     * @param string $serviceId
     * @return object
     */
    public function grabServiceFromContainer($serviceId) {
        return $this->kernel->getContainer()->get($serviceId);
    }

}
