<?php

namespace Tests\ShopBundle\Smoke\Http;

class TestCaseConfig
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
    private $delayedCustomizations;

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
        $this->delayedCustomizations = [];
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
     * @return \Tests\ShopBundle\Smoke\Http\TestCaseConfig
     */
    public function executeAllCustomizationsDelayedUntilTestExecution()
    {
        foreach ($this->delayedCustomizations as $customization) {
            $customization($this);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasCredentialsChanged()
    {
        return $this->credentialsChanged;
    }

    /**
     * @return \Tests\ShopBundle\Smoke\Http\TestCaseConfig
     */
    public function skip()
    {
        $this->skipped = true;

        return $this;
    }

    /**
     * @param string|null $password
     * @return \Tests\ShopBundle\Smoke\Http\TestCaseConfig
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
     * @return \Tests\ShopBundle\Smoke\Http\TestCaseConfig
     */
    public function expectStatusCode($code)
    {
        $this->expectedStatusCode = $code;

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return \Tests\ShopBundle\Smoke\Http\TestCaseConfig
     */
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * @param string $debugNote
     * @return \Tests\ShopBundle\Smoke\Http\TestCaseConfig
     */
    public function addDebugNote($debugNote)
    {
        $this->debugNotes[] = $debugNote;

        return $this;
    }

    /**
     * @param callable $callback
     * @return \Tests\ShopBundle\Smoke\Http\TestCaseConfig
     */
    public function delayCustomizationUntilTestExecution($callback)
    {
        $this->delayedCustomizations[] = $callback;

        return $this;
    }
}
