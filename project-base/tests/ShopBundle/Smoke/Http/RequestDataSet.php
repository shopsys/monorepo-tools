<?php

namespace Tests\ShopBundle\Smoke\Http;

class RequestDataSet
{
    /**
     * @var string
     */
    private $routeName;

    /**
     * @var bool
     */
    private $skipped;

    /**
     * @var string|null
     */
    private $username;

    /**
     * @var string|null
     */
    private $password;

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
     * @var bool
     */
    private $credentialsChanged;

    /**
     * @param string $routeName
     * @param int|null $expectedStatusCode
     */
    public function __construct($routeName, $expectedStatusCode = null)
    {
        $this->routeName = $routeName;
        $this->expectedStatusCode = $expectedStatusCode;
        $this->skipped = false;
        $this->parameters = [];
        $this->debugNotes = [];
        $this->callsDuringTestExecution = [];
        $this->credentialsChanged = false;
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
     * @return string|null
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return int|null
     */
    public function getExpectedStatusCode()
    {
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
     * @return \Tests\ShopBundle\Smoke\Http\RequestDataSet
     */
    public function executeCallsDuringTestExecution()
    {
        foreach ($this->callsDuringTestExecution as $customization) {
            $customization($this);
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
     * @param string|null $password
     * @return \Tests\ShopBundle\Smoke\Http\RequestDataSet
     */
    public function setCredentials($username, $password)
    {
        $this->credentialsChanged = true;
        $this->username = $username;
        $this->password = $password;

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
     * Provided $callback will be called with this instance as a single argument
     *
     * Useful for code that need to access the same instance of kernel as the test method.
     *
     * @see \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase::$kernel
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
        if ($requestDataSet->credentialsChanged) {
            $this->setCredentials(
                $requestDataSet->getUsername(),
                $requestDataSet->getPassword()
            );
        }
        if ($requestDataSet->getExpectedStatusCode() !== null) {
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
