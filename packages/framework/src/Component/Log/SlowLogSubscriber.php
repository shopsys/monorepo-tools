<?php

namespace Shopsys\FrameworkBundle\Component\Log;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SlowLogSubscriber implements EventSubscriberInterface
{
    const REQUEST_TIME_LIMIT_SECONDS = 2;

    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    protected $logger;

    /**
     * @var float
     */
    protected $startTime;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
        $this->startTime = 0;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function initStartTime(GetResponseEvent $event)
    {
        if ($event->isMasterRequest()) {
            $this->startTime = microtime(true);
        }
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\PostResponseEvent $event
     */
    public function addNotice(PostResponseEvent $event)
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
    protected function getRequestTime()
    {
        return microtime(true) - $this->startTime;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'initStartTime',
            KernelEvents::TERMINATE => 'addNotice',
        ];
    }
}
