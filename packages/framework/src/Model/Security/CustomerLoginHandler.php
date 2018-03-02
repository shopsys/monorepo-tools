<?php

namespace Shopsys\FrameworkBundle\Model\Security;

use Shopsys\FrameworkBundle\Component\Router\CurrentDomainRouter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class CustomerLoginHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Router\CurrentDomainRouter $router
     */
    public function __construct(CurrentDomainRouter $router)
    {
        $this->router = $router;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $referer = $request->headers->get('referer');
        if ($request->isXmlHttpRequest()) {
            $responseData = [
                'success' => true,
                'urlToRedirect' => $referer,
            ];

            return new JsonResponse($responseData);
        } else {
            return new RedirectResponse($referer);
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Security\Core\Exception\AuthenticationException $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->isXmlHttpRequest()) {
            $responseData = [
                'success' => false,
            ];

            return new JsonResponse($responseData);
        } else {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

            return new RedirectResponse($this->router->generate('front_login'));
        }
    }
}
