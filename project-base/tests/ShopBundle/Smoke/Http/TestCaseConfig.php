<?php

namespace Tests\ShopBundle\Smoke\Http;

class TestCaseConfig
{
    /**
     * @var string
     */
    private $routeName;

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
    private $notes;

    /**
     * @param string $routeName
     * @param int|null $expectedStatusCode
     */
    public function __construct($routeName, $expectedStatusCode = null)
    {
        $this->routeName = $routeName;
        $this->expectedStatusCode = $expectedStatusCode;
        $this->parameters = [];
        $this->notes = [];
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        return $this->routeName;
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
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string|null $username
     * @param string|null $password
     * @return \Tests\ShopBundle\Smoke\Http\TestCaseConfig
     */
    public function setCredentials($username, $password)
    {
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
     * @param string $note
     * @return \Tests\ShopBundle\Smoke\Http\TestCaseConfig
     */
    public function addNote($note)
    {
        $this->notes[] = $note;

        return $this;
    }
}
