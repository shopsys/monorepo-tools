<?php

namespace Tests\ShopBundle\Smoke\Http;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\ShopBundle\Smoke\Http\Auth\AuthInterface;
use Tests\ShopBundle\Smoke\Http\Auth\NoAuth;

class RequestDataSet
{
    const DEFAULT_EXPECTED_STATUS_CODE = 200;

    /**
     * @var string
     */
    private $routeName;

    /**
     * @var bool
     */
    private $skipped;

    /**
     * @var \Tests\ShopBundle\Smoke\Http\Auth\AuthInterface|null
     */
    private $auth;

    /**
     * @var int|null
     */
    private $expectedStatusCode;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var string[]
     */
    private $debugNotes;

    /**
     * @var callable[]
     */
    private $callsDuringTestExecution;

    /**
     * @param string $routeName
     */
    public function __construct($routeName)
    {
        $this->routeName = $routeName;
        $this->skipped = false;
        $this->parameters = [];
        $this->debugNotes = [];
        $this->callsDuringTestExecution = [];
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * @return bool
     */
    public function isSkipped()
    {
        return $this->skipped;
    }

    /**
     * @return \Tests\ShopBundle\Smoke\Http\Auth\AuthInterface
     */
    public function getAuth()
    {
        if ($this->auth === null) {
            return new NoAuth();
        }

        return $this->auth;
    }

    /**
     * @return int
     */
    public function getExpectedStatusCode()
    {
        if ($this->expectedStatusCode === null) {
            return self::DEFAULT_EXPECTED_STATUS_CODE;
        }

        return $this->expectedStatusCode;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string[]
     */
    public function getDebugNotes()
    {
        return $this->debugNotes;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @return \Tests\ShopBundle\Smoke\Http\RequestDataSet
     */
    public function executeCallsDuringTestExecution(ContainerInterface $container)
    {
        foreach ($this->callsDuringTestExecution as $customization) {
            $customization($this, $container);
        }

        return $this;
    }

    /**
     * @return \Tests\ShopBundle\Smoke\Http\RequestDataSet
     */
    public function skip()
    {
        $this->skipped = true;

        return $this;
    }

    /**
     * @param \Tests\ShopBundle\Smoke\Http\Auth\AuthInterface $auth
     * @return \Tests\ShopBundle\Smoke\Http\RequestDataSet
     */
    public function setAuth(AuthInterface $auth)
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * @param int $code
     * @return \Tests\ShopBundle\Smoke\Http\RequestDataSet
     */
    public function expectStatusCode($code)
    {
        $this->expectedStatusCode = $code;

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return \Tests\ShopBundle\Smoke\Http\RequestDataSet
     */
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * @param string $debugNote
     * @return \Tests\ShopBundle\Smoke\Http\RequestDataSet
     */
    public function addDebugNote($debugNote)
    {
        $this->debugNotes[] = $debugNote;

        return $this;
    }

    /**
     * Provided $callback will be called with instance of this and ContainerInterface as arguments
     *
     * Useful for code that needs to access the same instance of container as the test method.
     *
     * @param callable $callback
     * @return \Tests\ShopBundle\Smoke\Http\RequestDataSet
     */
    public function addCallDuringTestExecution($callback)
    {
        $this->callsDuringTestExecution[] = $callback;

        return $this;
    }

    /**
     * Merges values from specified $requestDataSet into this instance.
     *
     * It is used to merge extra RequestDataSet into default RequestDataSet.
     * Values that were not specified in $requestDataSet have no effect on result.
     *
     * @param \Tests\ShopBundle\Smoke\Http\RequestDataSet $requestDataSet
     * @return \Tests\ShopBundle\Smoke\Http\RequestDataSet
     */
    public function mergeExtraValuesFrom(RequestDataSet $requestDataSet)
    {
        if ($requestDataSet->auth !== null) {
            $this->setAuth($requestDataSet->getAuth());
        }
        if ($requestDataSet->expectedStatusCode !== null) {
            $this->expectStatusCode($requestDataSet->getExpectedStatusCode());
        }
        foreach ($requestDataSet->getParameters() as $name => $value) {
            $this->setParameter($name, $value);
        }
        foreach ($requestDataSet->getDebugNotes() as $debugNote) {
            $this->addDebugNote($debugNote);
        }

        return $this;
    }
}
