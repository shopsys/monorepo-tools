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
     * @var bool
     */
    private $ignored;

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
        $this->ignored = false;
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
    public function isMethodAllowed($method)
    {
        $methods = $this->route->getMethods();

        return count($methods) === 0 || in_array(strtoupper($method), $methods, true);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isParameterRequired($name)
    {
        return !$this->route->hasDefault($name) && in_array($name, $this->getRoutePathParameters(), true);
    }

    /**
     * @return string[]
     */
    public function getRoutePathParameters()
    {
        $matches = [];
        preg_match_all('~\{([^}]+)\}~', $this->route->getPath(), $matches);

        return $matches[1];
    }

    /**
     * @return \Tests\ShopBundle\Smoke\Http\TestCaseConfig[]
     */
    public function getTestCaseConfigs()
    {
        $testCaseConfigs = [];
        if (!$this->ignored) {
            $testCaseConfigs[] = $this->defaultTestCaseConfig;
            foreach ($this->additionalTestCaseConfigs as $additionalTestCaseConfig) {
                $testCaseConfig = clone $this->defaultTestCaseConfig;
                if ($additionalTestCaseConfig->getUsername() !== null) {
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
                $testCaseConfigs[] = $testCaseConfig;
            }
        }

        return $testCaseConfigs;
    }

    /**
     * @return \Tests\ShopBundle\Smoke\Http\RouteConfig
     */
    public function ignore()
    {
        $this->ignored = true;

        return $this;
    }

    /**
     * @param $username
     * @param $password
     * @return \Tests\ShopBundle\Smoke\Http\RouteConfig
     */
    public function setCredentials($username, $password)
    {
        $this->defaultTestCaseConfig->setCredentials($username, $password);

        return $this;
    }

    /**
     * @param int $code
     * @return \Tests\ShopBundle\Smoke\Http\RouteConfig
     */
    public function expectStatusCode($code)
    {
        $this->defaultTestCaseConfig->expectStatusCode($code);

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return \Tests\ShopBundle\Smoke\Http\RouteConfig
     */
    public function setParameter($name, $value)
    {
        $this->defaultTestCaseConfig->setParameter($name, $value);

        return $this;
    }

    /**
     * @return \Tests\ShopBundle\Smoke\Http\TestCaseConfig
     */
    public function addTestCase()
    {
        $testCaseConfig = new TestCaseConfig($this->routeName);
        $this->additionalTestCaseConfigs[] = $testCaseConfig;

        return $testCaseConfig;
    }
}
