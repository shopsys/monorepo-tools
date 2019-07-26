<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Test\Codeception\Helper;

use AppKernel;
use Codeception\Configuration;
use Codeception\Module;
use Codeception\TestInterface;
use Shopsys\FrameworkBundle\Component\Environment\EnvironmentType;

class SymfonyHelper extends Module
{
    /**
     * @var \Symfony\Component\HttpKernel\Kernel
     */
    private $kernel;

    /**
     * {@inheritDoc}
     */
    public function _initialize()
    {
        require_once Configuration::projectDir() . '/../app/autoload.php';

        $this->kernel = new AppKernel(EnvironmentType::TEST, EnvironmentType::isDebug(EnvironmentType::TEST));
        $this->kernel->boot();
    }

    /**
     * {@inheritDoc}
     */
    public function _before(TestInterface $test)
    {
        $this->kernel->boot();
    }

    /**
     * @param string $serviceId
     * @return object
     */
    public function grabServiceFromContainer($serviceId)
    {
        return $this->kernel->getContainer()->get($serviceId);
    }
}
