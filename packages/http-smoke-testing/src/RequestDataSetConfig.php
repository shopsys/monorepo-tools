<?php

namespace Shopsys\HttpSmokeTesting;

use Shopsys\HttpSmokeTesting\Auth\AuthInterface;

interface RequestDataSetConfig
{
    /**
     * @param \Shopsys\HttpSmokeTesting\Auth\AuthInterface $auth
     * @return $this
     */
    public function setAuth(AuthInterface $auth);

    /**
     * @param int $code
     * @return $this
     */
    public function setExpectedStatusCode($code);

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setParameter($name, $value);

    /**
     * @param string $debugNote
     * @return $this
     */
    public function addDebugNote($debugNote);

    /**
     * Provided $callback will be called with instance of this and ContainerInterface as arguments.
     *
     * Useful for code that needs to access the same instance of container as the test method.
     *
     * @param callable $callback
     * @return $this
     */
    public function addCallDuringTestExecution($callback);
}
