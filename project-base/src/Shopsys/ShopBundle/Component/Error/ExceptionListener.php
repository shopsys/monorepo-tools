<?php

namespace Shopsys\ShopBundle\Component\Error;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener {

	/**
	 * @var \Exception|null
	 */
	private $lastException;

	/**
	 * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
	 */
	public function onKernelException(GetResponseForExceptionEvent $event) {
		$this->lastException = $event->getException();
	}

	/**
	 * @return \Exception|null
	 */
	public function getLastException() {
		return $this->lastException;
	}

}
