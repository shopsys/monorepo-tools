<?php

namespace SS6\ShopBundle\Model\Domain;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DomainListener implements EventSubscriberInterface {

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	public function __construct(Domain $domain) {
		$this->domain = $domain;
	}

	/**
	 * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
	 */
	public function onKernelRequest(GetResponseEvent $event) {
		if ($event->isMasterRequest()) {
			try {
				$this->domain->getId();
			} catch (\SS6\ShopBundle\Model\Domain\Exception\NoDomainSelectedException $exception) {
				$this->domain->switchDomainByRequest($event->getRequest());
			}
		}
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents() {
		return array(
			// Set domain by request must be first for other services (eg.: routing, localization...)
			KernelEvents::REQUEST => array(array('onKernelRequest', 100)),
		);
	}

}
