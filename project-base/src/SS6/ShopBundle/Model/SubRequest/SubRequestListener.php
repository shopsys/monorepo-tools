<?php

namespace SS6\ShopBundle\Model\SubRequest;

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
		} else {
			$this->fillSubRequestFromMasterRequest($event->getRequest());
		}
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $subRequest
	 */
	private function fillSubRequestFromMasterRequest(Request $subRequest) {
		$subRequest->setMethod($this->masterRequest->getMethod());
		$subRequestParameterBag = $subRequest->request;
		$subRequestData = array_replace($this->masterRequest->request->all(), $subRequestParameterBag->all());
		$subRequestParameterBag->replace($subRequestData);
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
	 * @throws \SS6\ShopBundle\Model\Redirect\Exception\TooManyRedirectResponsesException
	 */
	private function processSubResponse(Response $subResponse) {
		if ($subResponse->isRedirection()) {
			if ($this->redirectResponse === null) {
				$this->redirectResponse = $subResponse;
			} else {
				$message = 'Only one subresponse can do a redirect.';
				throw new \SS6\ShopBundle\Model\SubRequest\Exception\TooManyRedirectResponsesException($message);
			}
		}
	}

}
