<?php

namespace Tests\ShopBundle\Smoke\Http\Auth;

use Symfony\Component\HttpFoundation\Request;

class BasicHttpAuth implements AuthInterface
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var null|string
     */
    private $password;

    /**
     * @param string $username
     * @param string|null $password
     */
    public function __construct($username, $password = null)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function authenticateRequest(Request $request)
    {
        $request->server->set('PHP_AUTH_USER', $this->username);
        if ($this->password !== null) {
            $request->server->set('PHP_AUTH_PW', $this->password);
        }

        $request->headers->add($request->server->getHeaders());
    }
}
