<?php

namespace Shopsys\FrameworkBundle\Component\Log;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;

class SlowLogFacade
{
    const REQUEST_TIME_LIMIT_SECONDS = 2;

    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    private $logger;

    /**
     * @var float
     */
    private $startTime;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
        $this->startTime = 0;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->isMasterRequest()) {
            $this->startTime = microtime(true);
        }
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\PostResponseEvent $event
     */
    public function onKernelTerminate(PostResponseEvent $event)
    {
        $requestTime = $this->getRequestTime();
        if ($requestTime > self::REQUEST_TIME_LIMIT_SECONDS) {
            $requestUri = $event->getRequest()->getRequestUri();
            $controllerNameAndAction = $event->getRequest()->get('_controller');

            $message = $requestTime . ' ' . $controllerNameAndAction . ' ' . $requestUri;
            $this->logger->addNotice($message);
        }
    }

    /**
     * @return float
     */
    private function getRequestTime()
    {
        return microtime(true) - $this->startTime;
    }
}
