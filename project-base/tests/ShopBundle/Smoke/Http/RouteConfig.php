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
     * @var \Tests\ShopBundle\Smoke\Http\RequestDataSet
     */
    private $defaultRequestDataSet;

    /**
     * @var \Tests\ShopBundle\Smoke\Http\RequestDataSet[]
     */
    private $extraRequestDataSets;

    /**
     * @param string $routeName
     * @param \Symfony\Component\Routing\Route $route
     */
    public function __construct($routeName, Route $route)
    {
        $this->routeName = $routeName;
        $this->route = $route;
        $this->defaultRequestDataSet = new RequestDataSet($this->routeName, 200);
        $this->extraRequestDataSets = [];
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
     * @return \Tests\ShopBundle\Smoke\Http\RequestDataSet[]
     */
    public function generateRequestDataSets()
    {
        $requestDataSets = [clone $this->defaultRequestDataSet];
        foreach ($this->extraRequestDataSets as $extraRequestDataSet) {
            $requestDataSet = clone $this->defaultRequestDataSet;
            if ($extraRequestDataSet->hasCredentialsChanged()) {
                $requestDataSet->setCredentials(
                    $extraRequestDataSet->getUsername(),
                    $extraRequestDataSet->getPassword()
                );
            }
            if ($extraRequestDataSet->getExpectedStatusCode() !== null) {
                $requestDataSet->expectStatusCode($extraRequestDataSet->getExpectedStatusCode());
            }
            foreach ($extraRequestDataSet->getParameters() as $name => $value) {
                $requestDataSet->setParameter($name, $value);
            }
            foreach ($extraRequestDataSet->getDebugNotes() as $debugNote) {
                $requestDataSet->addDebugNote($debugNote);
            }
            $requestDataSets[] = $requestDataSet;
        }

        return $requestDataSets;
    }

    /**
     * @param string|null $debugNote
     * @return \Tests\ShopBundle\Smoke\Http\RouteConfig
     */
    public function skipRoute($debugNote = null)
    {
        $this->defaultRequestDataSet->skip();

        if ($debugNote !== null) {
            $this->defaultRequestDataSet->addDebugNote('Skipped test case: ' . $debugNote);
        }

        return $this;
    }

    /**
     * @param string|null $debugNote
     * @return \Tests\ShopBundle\Smoke\Http\RequestDataSet
     */
    public function changeDefaultRequestDataSet($debugNote = null)
    {
        $requestDataSet = $this->defaultRequestDataSet;

        if ($debugNote !== null) {
            $requestDataSet->addDebugNote($debugNote);
        }

        return $requestDataSet;
    }

    /**
     * @param string|null $debugNote
     * @return \Tests\ShopBundle\Smoke\Http\RequestDataSet
     */
    public function addExtraRequestDataSet($debugNote = null)
    {
        $requestDataSet = new RequestDataSet($this->routeName);
        $this->extraRequestDataSets[] = $requestDataSet;

        if ($debugNote !== null) {
            $requestDataSet->addDebugNote('Extra test case: ' . $debugNote);
        }

        return $requestDataSet;
    }

    /**
     * Provided $callback will be called with RequestDataSet as a single argument
     *
     * Useful for code that need to access the same instance of kernel as the test method.
     *
     * @see \Tests\ShopBundle\Smoke\Http\RequestDataSet
     * @see \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase::$kernel
     *
     * @param callable $callback
     * @return \Tests\ShopBundle\Smoke\Http\RouteConfig
     */
    public function addCallDuringTestExecution($callback)
    {
        $this->defaultRequestDataSet->addCallDuringTestExecution($callback);

        return $this;
    }
}
