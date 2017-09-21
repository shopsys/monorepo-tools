<?php

namespace Shopsys\ShopBundle\Component\Domain;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class DomainAwareSecurityHeadersSetter
{
    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if ($this->domain->isHttps()) {
            // Do not allow to external content from non-HTTPS URLs.
            // Other security features stays as if CSP was not used:
            // - allow inline JavaScript and CSS
            // - allow eval() function in JavaScript
            // - allow data URLs
            $event->getResponse()->headers->set(
                'Content-Security-Policy',
                "default-src https: 'unsafe-inline' 'unsafe-eval' data:"
            );
        }
    }
}
