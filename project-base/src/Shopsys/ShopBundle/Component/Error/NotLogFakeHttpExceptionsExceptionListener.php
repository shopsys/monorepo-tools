<?php

namespace SS6\ShopBundle\Component\Error;

use Symfony\Component\HttpKernel\EventListener\ExceptionListener;

class NotLogFakeHttpExceptionsExceptionListener extends ExceptionListener {

	/**
	 * @inheritDoc
	 */
	protected function logException(\Exception $exception, $message) {
		if (!$exception instanceof \SS6\ShopBundle\Component\Error\Exception\FakeHttpException) {
			parent::logException($exception, $message);
		}
	}

}
