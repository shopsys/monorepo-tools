<?php

namespace SS6\ShopBundle\Model\Redirect;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class RedirectFragmentHandler extends FragmentHandler {

	/**
	 * {@inheritdoc}
	 */
	protected function deliver(Response $response) {
		// Redirect response is OK, because SubResponseRedirectListener make redirect instead of master response
		if (!$response->isSuccessful() && !$response->isRedirection()) {
			$message = sprintf(
				'Error when rendering "%s" (Status code is %s).',
				$this->getRequest()->getUri(),
				$response->getStatusCode()
			);
			throw new \RuntimeException($message);
		}

		if (!$response instanceof StreamedResponse) {
			return $response->getContent();
		}

		$response->sendContent();
	}

}
