<?php

namespace Shopsys\ShopBundle\Component\HttpFoundation;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class SubRequestListener {

	/**
	 * @var \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	private $redirectResponse;

	/**
	 * @var \Symfony\Component\HttpFoundation\Request
	 */
	private $masterRequest;

	/**
	 * @param \Symfony\Component\HttpKernel\Event\FilterControllerEvent $event
	 */
	public function onKernelController(FilterControllerEvent $event) {
		if ($event->isMasterRequest()) {
			$this->masterRequest = $event->getRequest();
		} elseif ($this->masterRequest !== null) {
			$this->fillSubRequestFromMasterRequest($event->getRequest());
		}
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $subRequest
	 */
	private function fillSubRequestFromMasterRequest(Request $subRequest) {
		$subRequest->setMethod($this->masterRequest->getMethod());
		$subRequest->request = $this->masterRequest->request;
		$subRequest->server = $this->masterRequest->server;
		$subRequest->files = $this->masterRequest->files;

		$subRequestQueryParameters = array_replace($this->masterRequest->query->all(), $subRequest->query->all());
		$subRequest->query->replace($subRequestQueryParameters);
	}

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
	 * @param \Symfony\Component\HttpFoundation\Response $subResponse
	 */
	private function processSubResponse(Response $subResponse) {
		if ($subResponse->isRedirection()) {
			if ($this->redirectResponse === null) {
				$this->redirectResponse = $subResponse;
			} else {
				$message = 'Only one subresponse can do a redirect.';
				throw new \Shopsys\ShopBundle\Component\HttpFoundation\Exception\TooManyRedirectResponsesException($message);
			}
		}
	}

}
