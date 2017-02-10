<?php

namespace Shopsys\ShopBundle\Component\Router\Security;

use Doctrine\Common\Annotations\Reader;
use ReflectionMethod;
use Shopsys\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class RouteCsrfProtector implements EventSubscriberInterface
{
    const CSRF_TOKEN_REQUEST_PARAMETER = 'routeCsrfToken';
    const CSRF_TOKEN_ID_PREFIX = 'route_';

    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    private $annotationReader;

    /**
     * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    private $tokenManager;

    public function __construct(Reader $annotationReader, CsrfTokenManagerInterface $tokenManager)
    {
        $this->annotationReader = $annotationReader;
        $this->tokenManager = $tokenManager;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if ($this->isProtected($event)) {
            $request = $event->getRequest();
            $csrfToken = $request->get(self::CSRF_TOKEN_REQUEST_PARAMETER);
            $routeName = $request->get('_route');

            if ($csrfToken === null || !$this->isCsrfTokenValid($routeName, $csrfToken)) {
                throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('Csrf token is invalid');
            }
        }
    }

    /**
     * @param string $routeName
     * @return string
     */
    public function getCsrfTokenId($routeName)
    {
        return self::CSRF_TOKEN_ID_PREFIX . $routeName;
    }

    /**
     * @param string $routeName
     * @return string
     */
    public function getCsrfTokenByRoute($routeName)
    {
        return $this->tokenManager->getToken($this->getCsrfTokenId($routeName))->getValue();
    }

    /**
     * @param string $routeName
     * @param string $csrfToken
     * @return bool
     */
    private function isCsrfTokenValid($routeName, $csrfToken)
    {
        $token = new CsrfToken($this->getCsrfTokenId($routeName), $csrfToken);

        return $this->tokenManager->isTokenValid($token);
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\FilterControllerEvent $event
     * @return bool
     */
    private function isProtected(FilterControllerEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return false;
        }

        list($controller, $action) = $event->getController();
        $method = new ReflectionMethod($controller, $action);
        $annotation = $this->annotationReader->getMethodAnnotation($method, CsrfProtection::class);

        return $annotation !== null;
    }
}
