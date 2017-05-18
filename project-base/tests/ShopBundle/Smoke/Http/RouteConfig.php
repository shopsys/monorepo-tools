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
    private $additionalRequestDataSets;

    /**
     * @param string $routeName
     * @param \Symfony\Component\Routing\Route $route
     */
    public function __construct($routeName, Route $route)
    {
        $this->routeName = $routeName;
        $this->route = $route;
        $this->defaultRequestDataSet = new RequestDataSet($this->routeName, 200);
        $this->additionalRequestDataSets = [];
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
        foreach ($this->additionalRequestDataSets as $additionalRequestDataSet) {
            $requestDataSet = clone $this->defaultRequestDataSet;
            if ($additionalRequestDataSet->hasCredentialsChanged()) {
                $requestDataSet->setCredentials(
                    $additionalRequestDataSet->getUsername(),
                    $additionalRequestDataSet->getPassword()
                );
            }
            if ($additionalRequestDataSet->getExpectedStatusCode() !== null) {
                $requestDataSet->expectStatusCode($additionalRequestDataSet->getExpectedStatusCode());
            }
            foreach ($additionalRequestDataSet->getParameters() as $name => $value) {
                $requestDataSet->setParameter($name, $value);
            }
            foreach ($additionalRequestDataSet->getDebugNotes() as $debugNote) {
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
    public function addRequestDataSet($debugNote = null)
    {
        $requestDataSet = new RequestDataSet($this->routeName);
        $this->additionalRequestDataSets[] = $requestDataSet;

        if ($debugNote !== null) {
            $requestDataSet->addDebugNote('Special test case: ' . $debugNote);
        }

        return $requestDataSet;
    }

    /**
     * @param callable $callback
     * @return \Tests\ShopBundle\Smoke\Http\RouteConfig
     */
    public function delayCustomizationUntilTestExecution($callback)
    {
        $this->defaultRequestDataSet->delayCustomizationUntilTestExecution($callback);

        return $this;
    }
}
