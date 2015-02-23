<?php

namespace SS6\ShopBundle\Component\Router\FriendlyUrl\Exception;

use Doctrine\Common\Util\Debug;
use Exception;
use SS6\ShopBundle\Component\Router\FriendlyUrl\Exception\FriendlyUrlException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FriendlyUrlNotFoundException extends NotFoundHttpException implements FriendlyUrlException {

	/**
	 * @param string|array $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		if (is_array($criteria)) {
			$message = 'Friendly url not found by criteria ' . Debug::export($criteria);
		} else {
			$message = $criteria;
		}
		parent::__construct($message, $previous, 0);
	}
}
