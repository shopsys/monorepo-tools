<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Exception;
use Shopsys\Environment;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Domain\Exception\UnableToResolveDomainException;
use Shopsys\FrameworkBundle\Component\Environment\EnvironmentType;
use Shopsys\FrameworkBundle\Component\Error\ErrorPagesFacade;
use Shopsys\FrameworkBundle\Component\Error\ExceptionController;
use Shopsys\FrameworkBundle\Component\Error\ExceptionListener;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Tracy\BlueScreen;
use Tracy\Debugger;

class ErrorController extends FrontBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Error\ExceptionController
     */
    private $exceptionController;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Error\ExceptionListener
     */
    private $exceptionListener;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Error\ErrorPagesFacade
     */
    private $errorPagesFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Error\ExceptionController $exceptionController
     * @param \Shopsys\FrameworkBundle\Component\Error\ExceptionListener $exceptionListener
     * @param \Shopsys\FrameworkBundle\Component\Error\ErrorPagesFacade $errorPagesFacade
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
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
    public function errorPageAction($code)
    {
        $this->exceptionController->setDebug(false);
        $this->exceptionController->setShowErrorPagePrototype();

        throw new \Shopsys\FrameworkBundle\Component\Error\Exception\FakeHttpException($code);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Debug\Exception\FlattenException $exception
     * @param \Symfony\Component\HttpKernel\Log\DebugLoggerInterface $logger
     */
    public function showAction(
        Request $request,
        FlattenException $exception,
        ?DebugLoggerInterface $logger = null
    ) {
        if ($this->isUnableToResolveDomainInNotDebug($exception)) {
            return $this->createUnableToResolveDomainResponse($request);
        } elseif ($this->exceptionController->isShownErrorPagePrototype()) {
            return $this->createErrorPagePrototypeResponse($request, $exception, $logger);
        } elseif ($this->exceptionController->getDebug()) {
            return $this->createExceptionResponse($request, $exception, $logger);
        } else {
            return $this->createErrorPageResponse($exception->getStatusCode());
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Debug\Exception\FlattenException $exception
     * @param \Symfony\Component\HttpKernel\Log\DebugLoggerInterface $logger
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function createErrorPagePrototypeResponse(
        Request $request,
        FlattenException $exception,
        DebugLoggerInterface $logger
    ) {
        // Same as in \Symfony\Bundle\TwigBundle\Controller\PreviewErrorController
        $format = $request->getRequestFormat();

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
    private function createErrorPageResponse($statusCode)
    {
        $errorPageStatusCode = $this->errorPagesFacade->getErrorPageStatusCodeByStatusCode($statusCode);
        $errorPageContent = $this->errorPagesFacade->getErrorPageContentByDomainIdAndStatusCode(
            $this->domain->getId(),
            $errorPageStatusCode
        );

        return new Response($errorPageContent, $errorPageStatusCode);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Debug\Exception\FlattenException $exception
     * @param \Symfony\Component\HttpKernel\Log\DebugLoggerInterface $logger
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function createExceptionResponse(Request $request, FlattenException $exception, DebugLoggerInterface $logger)
    {
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
    private function getPrettyExceptionResponse(Exception $exception)
    {
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

    /**
     * @param \Symfony\Component\Debug\Exception\FlattenException $exception
     * @return bool
     */
    private function isUnableToResolveDomainInNotDebug(FlattenException $exception): bool
    {
        if ($this->exceptionController->getDebug()) {
            return false;
        }

        return $exception->getClass() === UnableToResolveDomainException::class;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function createUnableToResolveDomainResponse(Request $request): Response
    {
        $url = $request->getSchemeAndHttpHost() . $request->getBasePath();
        $content = sprintf("You are trying to access an unknown domain '%s'.", $url);

        if (Environment::getEnvironment(false) === EnvironmentType::TEST) {
            $overwriteDomainUrl = $this->getParameter('overwrite_domain_url');
            $content .= sprintf(" TEST environment is active, current domain url is '%s'.", $overwriteDomainUrl);
        }

        return new Response($content, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
