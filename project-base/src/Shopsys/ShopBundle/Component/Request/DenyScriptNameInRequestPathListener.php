<?php

namespace Shopsys\ShopBundle\Component\Request;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class DenyScriptNameInRequestPathListener implements EventSubscriberInterface
{
    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->isMasterRequest()) {
            $request = $event->getRequest();
            if ($request->getBasePath() !== $request->getBaseUrl()) {
                throw new NotFoundHttpException(
                    'Requested URL contains script file name (/index.php). Access to an URL with script file is denied '
                        . 'to avoid duplicate content (even to /index.php/_wdt/* which is why web debug toolbar does '
                        . 'not work). Having script file in URL is used in Symfony to change front controller (and '
                        . 'therefore the environment) but we do not need this.'
                );
            }
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
