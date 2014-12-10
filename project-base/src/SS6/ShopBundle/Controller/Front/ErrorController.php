<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Component\ExceptionController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends Controller {

	/**
	 * @param int $code
	 */
	public function errorPageAction($code) {
		/* @var $exceptionController \SS6\ShopBundle\Component\ExceptionController */
		$exceptionController = $this->get('twig.controller.exception');

		if ($exceptionController instanceof ExceptionController) {
			$exceptionController->setDebug(false);
		}

		throw new \Symfony\Component\HttpKernel\Exception\HttpException($code);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param \Symfony\Component\HttpKernel\Exception\FlattenException $exception
	 * @param \Symfony\Component\HttpKernel\Log\DebugLoggerInterface $logger
	 * @param string $format
	 */
	public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null,
			$format = 'html') {
		$exceptionController = $this->get('twig.controller.exception');
		/* @var $exceptionController \SS6\ShopBundle\Component\ExceptionController */

		if ($exceptionController instanceof ExceptionController) {
			if (!$exceptionController->getDebug()) {
				$code = $exception->getStatusCode();
				return $this->render('@SS6Shop/Front/Content/Error/error.' . $format . '.twig', array(
					'status_code' => $code,
					'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
					'exception' => $exception,
					'logger' => $logger,
				));
			}
		}

		return $exceptionController->showAction($request, $exception, $logger, $format);
	}
}
