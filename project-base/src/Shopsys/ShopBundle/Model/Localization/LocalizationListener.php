<?php

namespace Shopsys\ShopBundle\Model\Localization;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocalizationListener implements EventSubscriberInterface
{
    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Model\Localization\Localization
     */
    private $localization;

    /**
     * @param \Shopsys\ShopBundle\Component\Domain\Domain $domain
     */
    public function __construct(
        Domain $domain,
        Localization $localization
    ) {
        $this->domain = $domain;
        $this->localization = $localization;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->isMasterRequest()) {
            $request = $event->getRequest();

            if ($this->isAdminRequest($request)) {
                $request->setLocale($this->localization->getAdminLocale());
            } else {
                $request->setLocale($this->domain->getLocale());
            }
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return bool
     */
    private function isAdminRequest(Request $request)
    {
        return preg_match('/^admin_/', $request->attributes->get('_route')) === 1;
    }

    public static function getSubscribedEvents()
    {
        return [
            // must be registered before the default Locale listener
            // see: http://symfony.com/doc/current/cookbook/session/locale_sticky_session.html
            KernelEvents::REQUEST => [['onKernelRequest', 17]],
        ];
    }
}
