<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Component\Collector;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\ShopsysFrameworkBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

final class ShopsysFrameworkDataCollector extends DataCollector
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(
        Domain $domain
    ) {
        $this->domain = $domain;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, ?\Exception $exception = null): void
    {
        $this->data = [
            'version' => ShopsysFrameworkBundle::VERSION,
            'docsVersion' => $this->resolveDocsVersion(ShopsysFrameworkBundle::VERSION),
            'domains' => $this->domain->getAll(),
            'currentDomainId' => $this->domain->getId(),
            'currentDomainName' => $this->domain->getName(),
            'currentDomainLocale' => $this->domain->getLocale(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function reset(): void
    {
        $this->data = [];
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->data['version'];
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig[]
     */
    public function getDomains(): array
    {
        return $this->data['domains'];
    }

    /**
     * @return int
     */
    public function getCurrentDomainId(): int
    {
        return $this->data['currentDomainId'];
    }

    /**
     * @return string
     */
    public function getCurrentDomainName(): string
    {
        return $this->data['currentDomainName'];
    }

    /**
     * @return string
     */
    public function getCurrentDomainLocale(): string
    {
        return $this->data['currentDomainLocale'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'shopsys_framework_core';
    }

    /**
     * @return string
     */
    public function getDocsVersion(): string
    {
        return $this->data['docsVersion'];
    }

    /**
     * @param string $versionString
     * @return string
     */
    protected function resolveDocsVersion(string $versionString): string
    {
        if (strpos($versionString, '-dev') !== false) {
            return 'master';
        }

        return 'v' . $versionString;
    }
}
