<?php

namespace Shopsys\HttpSmokeTesting\Auth;

use Symfony\Component\HttpFoundation\Request;

interface AuthInterface
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function authenticateRequest(Request $request);
}
