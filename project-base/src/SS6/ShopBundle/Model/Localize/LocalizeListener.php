<?php

namespace SS6\ShopBundle\Model\Localize;

use SS6\ShopBundle\Model\Domain\Domain;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class LocalizeListener {

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @param \SS6\ShopBundle\Model\Domain\Domain $domain
	 */
	public function __construct(Domain $domain) {
		$this->domain = $domain;
	}

	/**
	 * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
	 */
	public function onKernelRequest(GetResponseEvent $event) {
		if ($event->isMasterRequest()) {
			$request = $event->getRequest();
			$request->setLocale($this->domain->getLocale());
		}
	}
}
