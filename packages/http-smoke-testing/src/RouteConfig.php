<?php

namespace Shopsys\HttpSmokeTesting;

interface RouteConfig
{
    /**
     * @param string|null $debugNote
     * @return \Shopsys\HttpSmokeTesting\RouteConfig
     */
    public function skipRoute($debugNote = null);

    /**
     * @param string|null $debugNote
     * @return \Shopsys\HttpSmokeTesting\RequestDataSet
     */
    public function changeDefaultRequestDataSet($debugNote = null);

    /**
     * @param string|null $debugNote
     * @return \Shopsys\HttpSmokeTesting\RequestDataSet
     */
    public function addExtraRequestDataSet($debugNote = null);
}
