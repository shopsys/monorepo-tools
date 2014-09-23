<?php

namespace SS6\ShopBundle\Model\Redirect;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class SubResponseRedirectListener {

	/**
	 * @var \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	private $redirectResponse;

	/**
	 * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
	 */
	public function onKernelResponse(FilterResponseEvent $event) {
		if ($event->isMasterRequest()) {
			if ($this->redirectResponse !== null) {
				$this->redirectResponse->send();
			}
		} else {
			$this->processSubResponse($event->getResponse());
		}
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Response $response
	 * @throws \SS6\ShopBundle\Model\Redirect\Exception\TooManyRedirectResponsesException
	 */
	private function processSubResponse(Response $response) {
		if ($response->isRedirection()) {
			if ($this->redirectResponse === null) {
				$this->redirectResponse = $response;
			} else {
				$message = 'Exists to many redirect responses while one master request.';
				throw new \SS6\ShopBundle\Model\Redirect\Exception\TooManyRedirectResponsesException($message);
			}
		}
	}

}
