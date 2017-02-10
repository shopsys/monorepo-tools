<?php

namespace Shopsys\ShopBundle\Component\Error;

use AppKernel;
use Shopsys\Environment;
use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Router\DomainRouterFactory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class ErrorPagesFacade
{
    const PAGE_STATUS_CODE_404 = Response::HTTP_NOT_FOUND;
    const PAGE_STATUS_CODE_500 = Response::HTTP_INTERNAL_SERVER_ERROR;

    /**
     * @var string
     */
    private $errorPagesDir;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\DomainRouterFactory
     */
    private $domainRouterFactory;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @param string $errorPagesDir
     * @param \Shopsys\ShopBundle\Component\Domain\Domain $domain
     * @param \Shopsys\ShopBundle\Component\Router\DomainRouterFactory $domainRouterFactory
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(
        $errorPagesDir,
        Domain $domain,
        DomainRouterFactory $domainRouterFactory,
        Filesystem $filesystem
    ) {
        $this->errorPagesDir = $errorPagesDir;
        $this->domain = $domain;
        $this->domainRouterFactory = $domainRouterFactory;
        $this->filesystem = $filesystem;
    }

    public function generateAllErrorPagesForProduction() {
        foreach ($this->domain->getAll() as $domainConfig) {
            $this->generateAndSaveErrorPage($domainConfig->getId(), self::PAGE_STATUS_CODE_404);
            $this->generateAndSaveErrorPage($domainConfig->getId(), self::PAGE_STATUS_CODE_500);
        }
    }

    /**
     * @param int $domainId
     * @param int $statusCode
     * @return string
     */
    public function getErrorPageContentByDomainIdAndStatusCode($domainId, $statusCode) {
        $errorPageContent = file_get_contents($this->getErrorPageFilename($domainId, $statusCode));
        if ($errorPageContent === false) {
            throw new \ShopBundle\Component\Error\Exception\ErrorPageNotFoundException($domainId, $statusCode);
        }

        return $errorPageContent;
    }

    /**
     * @param int $statusCode
     * @return int
     */
    public function getErrorPageStatusCodeByStatusCode($statusCode) {
        if ($statusCode === Response::HTTP_NOT_FOUND || $statusCode === Response::HTTP_FORBIDDEN) {
            return self::PAGE_STATUS_CODE_404;
        } else {
            return self::PAGE_STATUS_CODE_500;
        }
    }

    /**
     * @param int $domainId
     * @param int $statusCode
     */
    private function generateAndSaveErrorPage($domainId, $statusCode) {
        $domainRouter = $this->domainRouterFactory->getRouter($domainId);
        $errorPageUrl = $domainRouter->generate(
            'front_error_page_format',
            [
                '_format' => 'html',
                'code' => $statusCode,
            ],
            RouterInterface::ABSOLUTE_URL
        );

        $errorPageContent = $this->getUrlContent($errorPageUrl, $statusCode);

        $this->filesystem->dumpFile(
            $this->getErrorPageFilename($domainId, $statusCode),
            $errorPageContent
        );
    }

    /**
     * @param int $domainId
     * @param int $statusCode
     * @return string
     */
    private function getErrorPageFilename($domainId, $statusCode) {
        return $this->errorPagesDir . $statusCode . '_ ' . $domainId . '.html';
    }

    /**
     * @param string $errorPageUrl
     * @param int $expectedStatusCode
     * @return string
     */
    private function getUrlContent($errorPageUrl, $expectedStatusCode) {
        $errorPageKernel = new AppKernel(Environment::ENVIRONMENT_PRODUCTION, false);

        $errorPageFakeRequest = Request::create($errorPageUrl);

        $errorPageResponse = $errorPageKernel->handle($errorPageFakeRequest);
        $errorPageKernel->terminate($errorPageFakeRequest, $errorPageResponse);

        if ($expectedStatusCode !== $errorPageResponse->getStatusCode()) {
            throw new \ShopBundle\Component\Error\Exception\BadErrorPageStatusCodeException(
                $errorPageUrl,
                $expectedStatusCode,
                $errorPageResponse->getStatusCode()
            );
        }

        return $errorPageResponse->getContent();
    }
}
