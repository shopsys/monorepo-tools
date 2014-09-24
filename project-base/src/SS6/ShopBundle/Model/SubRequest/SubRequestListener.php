<?php

namespace SS6\ShopBundle\Model\SubRequest;

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
		} else {
			$event->getRequest()->setMethod($this->masterRequest->getMethod());
			$request = $event->getRequest()->request;
			$requestData = array_replace($this->masterRequest->request->all(), $request->all());
			$request->replace($requestData);
		}
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
	 * @param \Symfony\Component\HttpFoundation\Response $response
	 * @throws \SS6\ShopBundle\Model\Redirect\Exception\TooManyRedirectResponsesException
	 */
	private function processSubResponse(Response $response) {
		if ($response->isRedirection()) {
			if ($this->redirectResponse === null) {
				$this->redirectResponse = $response;
			} else {
				$message = 'Exists to many redirect responses while one master request.';
				throw new \SS6\ShopBundle\Model\SubRequest\Exception\TooManyRedirectResponsesException($message);
			}
		}
	}

}
