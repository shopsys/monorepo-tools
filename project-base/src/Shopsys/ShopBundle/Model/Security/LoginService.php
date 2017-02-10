<?php

namespace Shopsys\ShopBundle\Model\Security;

use Shopsys\ShopBundle\Model\Customer\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class LoginService
{

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage
     */
    private $tokenStorage;

    /**
     * @var \Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher
     */
    private $traceableEventDispatcher;

    public function __construct(
        TokenStorage $tokenStorage,
        TraceableEventDispatcher $traceableEventDispatcher
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->traceableEventDispatcher = $traceableEventDispatcher;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return bool
     */
    public function checkLoginProcess(Request $request) {
        $error = null;

        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } else {
            $session = $request->getSession();
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        }

        if ($error !== null) {
            throw new \Shopsys\ShopBundle\Model\Security\Exception\LoginFailedException('Login failed.');
        }

        return true;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Customer\User $user
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function loginUser(User $user, Request $request) {
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'frontend', $user->getRoles());
        $this->tokenStorage->setToken($token);

        // dispatch the login event
        $event = new InteractiveLoginEvent($request, $token);
        $this->traceableEventDispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $event);
    }
}
