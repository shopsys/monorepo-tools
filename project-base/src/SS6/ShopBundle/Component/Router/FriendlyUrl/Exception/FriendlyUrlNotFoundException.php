<?php

namespace SS6\ShopBundle\Component\Router\FriendlyUrl\Exception;

use Exception;
use SS6\ShopBundle\Component\Router\FriendlyUrl\Exception\FriendlyUrlException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FriendlyUrlNotFoundException extends NotFoundHttpException implements FriendlyUrlException {

	/**
	 * @param string $message
	 * @param \Exception $previous
	 */
	public function __construct($message = '', Exception $previous = null) {
		parent::__construct($message, $previous, 0);
	}
}
