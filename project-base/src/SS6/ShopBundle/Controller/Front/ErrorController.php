<?php

namespace SS6\ShopBundle\Controller\Front;

use Exception;
use SS6\ShopBundle\Component\Controller\FrontBaseController;
use SS6\ShopBundle\Component\Error\ExceptionController;
use SS6\ShopBundle\Component\Error\ExceptionListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Tracy\BlueScreen;
use Tracy\Debugger;

class ErrorController extends FrontBaseController {

	/**
	 * @var \SS6\ShopBundle\Component\Error\ExceptionController
	 */
	private $exceptionController;

	/**
	 * @var \SS6\ShopBundle\Component\Error\ExceptionListener
	 */
	private $exceptionListener;

	public function __construct(
		ExceptionController $exceptionController,
		ExceptionListener $exceptionListener
	) {
		$this->exceptionController = $exceptionController;
		$this->exceptionListener = $exceptionListener;
	}

	/**
	 * @param int $code
	 */
	public function errorPageAction($code) {
		$this->exceptionController->setDebug(false);

		throw new \Symfony\Component\HttpKernel\Exception\HttpException($code);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param \Symfony\Component\HttpKernel\Exception\FlattenException $exception
	 * @param \Symfony\Component\HttpKernel\Log\DebugLoggerInterface $logger
	 * @param string $format
	 */
	public function showAction(
		Request $request,
		FlattenException $exception,
		DebugLoggerInterface $logger = null,
		$format = 'html'
	) {
		if (!$this->exceptionController->getDebug()) {
			$code = $exception->getStatusCode();
			return $this->render('@SS6Shop/Front/Content/Error/error.' . $format . '.twig', [
				'status_code' => $code,
				'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
				'exception' => $exception,
				'logger' => $logger,
			]);
		}

		$lastException = $this->exceptionListener->getLastException();
		if ($lastException !== null) {
			return $this->getPrettyExceptionResponse($lastException);
		}

		return $this->exceptionController->showAction($request, $exception, $logger);
	}

	/**
	 * @param \Exception $exception
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	private function getPrettyExceptionResponse(Exception $exception) {
		Debugger::$time = time();
		$blueScreen = new BlueScreen();
		$blueScreen->info = [
			'PHP ' . PHP_VERSION,
		];

		ob_start();
		$blueScreen->render($exception);
		$blueScreenHtml = ob_get_contents();
		ob_end_clean();

		return new Response($blueScreenHtml);
	}
}
