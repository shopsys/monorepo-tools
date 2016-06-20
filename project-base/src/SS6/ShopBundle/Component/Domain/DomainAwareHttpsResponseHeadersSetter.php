<?php

namespace SS6\ShopBundle\Component\Domain;

use SS6\ShopBundle\Component\Domain\Domain;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class DomainAwareHttpsResponseHeadersSetter {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	public function __construct(Domain $domain) {
		$this->domain = $domain;
	}

	/**
	 * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
	 */
	public function onKernelResponse(FilterResponseEvent $event) {
		if (!$event->isMasterRequest()) {
			return;
		}

		if ($this->domain->isHttps()) {
			// do not allow to download content from non-HTTPS URLs
			$event->getResponse()->headers->set('Content-Security-Policy', 'default-src https:');
		}
	}

}
