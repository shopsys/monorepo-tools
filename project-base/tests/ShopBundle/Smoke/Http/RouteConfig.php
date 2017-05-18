<?php

namespace Tests\ShopBundle\Smoke\Http;

use Symfony\Component\Routing\Route;

class RouteConfig
{
    /**
     * @var string
     */
    private $routeName;

    /**
     * @var \Symfony\Component\Routing\Route
     */
    private $route;

    /**
     * @var \Tests\ShopBundle\Smoke\Http\TestCaseConfig
     */
    private $defaultTestCaseConfig;

    /**
     * @var \Tests\ShopBundle\Smoke\Http\TestCaseConfig[]
     */
    private $additionalTestCaseConfigs;

    /**
     * @param string $routeName
     * @param \Symfony\Component\Routing\Route $route
     */
    public function __construct($routeName, Route $route)
    {
        $this->routeName = $routeName;
        $this->route = $route;
        $this->defaultTestCaseConfig = new TestCaseConfig($this->routeName, 200);
        $this->additionalTestCaseConfigs = [];
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * @return string
     */
    public function getRoutePath()
    {
        return $this->route->getPath();
    }

    /**
     * @return string
     */
    public function getRouteCondition()
    {
        return $this->route->getCondition();
    }

    /**
     * @param string $method
     * @return bool
     */
    public function isHttpMethodAllowed($method)
    {
        $methods = $this->route->getMethods();

        return count($methods) === 0 || in_array(strtoupper($method), $methods, true);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isRouteParameterRequired($name)
    {
        return !$this->route->hasDefault($name) && in_array($name, $this->getRouteParameterNames(), true);
    }

    /**
     * @return string[]
     */
    public function getRouteParameterNames()
    {
        $compiledRoute = $this->route->compile();

        return $compiledRoute->getVariables();
    }

    /**
     * @return \Tests\ShopBundle\Smoke\Http\TestCaseConfig[]
     */
    public function generateTestCaseConfigs()
    {
        $testCaseConfigs = [clone $this->defaultTestCaseConfig];
        foreach ($this->additionalTestCaseConfigs as $additionalTestCaseConfig) {
            $testCaseConfig = clone $this->defaultTestCaseConfig;
            if ($additionalTestCaseConfig->hasCredentialsChanged()) {
                $testCaseConfig->setCredentials(
                    $additionalTestCaseConfig->getUsername(),
                    $additionalTestCaseConfig->getPassword()
                );
            }
            if ($additionalTestCaseConfig->getExpectedStatusCode() !== null) {
                $testCaseConfig->expectStatusCode($additionalTestCaseConfig->getExpectedStatusCode());
            }
            foreach ($additionalTestCaseConfig->getParameters() as $name => $value) {
                $testCaseConfig->setParameter($name, $value);
            }
            foreach ($additionalTestCaseConfig->getDebugNotes() as $debugNote) {
                $testCaseConfig->addDebugNote($debugNote);
            }
            $testCaseConfigs[] = $testCaseConfig;
        }

        return $testCaseConfigs;
    }

    /**
     * @param string|null $debugNote
     * @return \Tests\ShopBundle\Smoke\Http\RouteConfig
     */
    public function skipRoute($debugNote = null)
    {
        $this->defaultTestCaseConfig->skip();

        if ($debugNote !== null) {
            $this->defaultTestCaseConfig->addDebugNote('Skipped test case: ' . $debugNote);
        }

        return $this;
    }

    /**
     * @param string|null $debugNote
     * @return \Tests\ShopBundle\Smoke\Http\TestCaseConfig
     */
    public function changeDefaultTestCase($debugNote = null)
    {
        $testCaseConfig = $this->defaultTestCaseConfig;

        if ($debugNote !== null) {
            $testCaseConfig->addDebugNote($debugNote);
        }

        return $testCaseConfig;
    }

    /**
     * @param string|null $debugNote
     * @return \Tests\ShopBundle\Smoke\Http\TestCaseConfig
     */
    public function addTestCase($debugNote = null)
    {
        $testCaseConfig = new TestCaseConfig($this->routeName);
        $this->additionalTestCaseConfigs[] = $testCaseConfig;

        if ($debugNote !== null) {
            $testCaseConfig->addDebugNote('Special test case: ' . $debugNote);
        }

        return $testCaseConfig;
    }

    /**
     * @param callable $callback
     * @return \Tests\ShopBundle\Smoke\Http\RouteConfig
     */
    public function delayCustomizationUntilTestExecution($callback)
    {
        $this->defaultTestCaseConfig->delayCustomizationUntilTestExecution($callback);

        return $this;
    }
}
