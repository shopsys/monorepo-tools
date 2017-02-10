<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Exception;
use Shopsys\ShopBundle\Component\Controller\FrontBaseController;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Error\ErrorPagesFacade;
use Shopsys\ShopBundle\Component\Error\ExceptionController;
use Shopsys\ShopBundle\Component\Error\ExceptionListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Tracy\BlueScreen;
use Tracy\Debugger;

class ErrorController extends FrontBaseController {

	/**
	 * @var \Shopsys\ShopBundle\Component\Error\ExceptionController
	 */
	private $exceptionController;

	/**
	 * @var \Shopsys\ShopBundle\Component\Error\ExceptionListener
	 */
	private $exceptionListener;

	/**
	 * @var \Shopsys\ShopBundle\Component\Error\ErrorPagesFacade
	 */
	private $errorPagesFacade;

	/**
	 * @var \Shopsys\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	public function __construct(
		ExceptionController $exceptionController,
		ExceptionListener $exceptionListener,
		ErrorPagesFacade $errorPagesFacade,
		Domain $domain
	) {
		$this->exceptionController = $exceptionController;
		$this->exceptionListener = $exceptionListener;
		$this->errorPagesFacade = $errorPagesFacade;
		$this->domain = $domain;
	}

	/**
	 * @param int $code
	 */
	public function errorPageAction($code) {
		$this->exceptionController->setDebug(false);
		$this->exceptionController->setShowErrorPagePrototype();

		throw new \Shopsys\ShopBundle\Component\Error\Exception\FakeHttpException($code);
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
		if ($this->exceptionController->isShownErrorPagePrototype()) {
			return $this->createErrorPagePrototypeResponse($exception, $logger, $format);
		} elseif ($this->exceptionController->getDebug()) {
			return $this->createExceptionResponse($request, $exception, $logger);
		} else {
			return $this->createErrorPageResponse($exception->getStatusCode());
		}
	}

	/**
	 * @param \Symfony\Component\HttpKernel\Exception\FlattenException $exception
	 * @param \Symfony\Component\HttpKernel\Log\DebugLoggerInterface $logger
	 * @param string $format
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	private function createErrorPagePrototypeResponse(FlattenException $exception, DebugLoggerInterface $logger, $format) {
		$code = $exception->getStatusCode();

		return $this->render('@ShopsysShop/Front/Content/Error/error.' . $format . '.twig', [
			'status_code' => $code,
			'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
			'exception' => $exception,
			'logger' => $logger,
		]);
	}

	/**
	 * @param int $statusCode
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	private function createErrorPageResponse($statusCode) {
		$errorPageStatusCode = $this->errorPagesFacade->getErrorPageStatusCodeByStatusCode($statusCode);
		$errorPageContent = $this->errorPagesFacade->getErrorPageContentByDomainIdAndStatusCode(
			$this->domain->getId(),
			$errorPageStatusCode
		);

		return new Response($errorPageContent, $errorPageStatusCode);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param \Symfony\Component\HttpKernel\Exception\FlattenException $exception
	 * @param \Symfony\Component\HttpKernel\Log\DebugLoggerInterface $logger
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	private function createExceptionResponse(Request $request, FlattenException $exception, DebugLoggerInterface $logger) {
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
