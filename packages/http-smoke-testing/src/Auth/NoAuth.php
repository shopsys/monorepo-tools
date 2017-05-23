<?php

namespace Shopsys\HttpSmokeTesting\Auth;

use Symfony\Component\HttpFoundation\Request;

class NoAuth implements AuthInterface
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function authenticateRequest(Request $request)
    {
    }
}
