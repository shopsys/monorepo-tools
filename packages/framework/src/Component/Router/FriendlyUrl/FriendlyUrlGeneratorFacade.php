<?php

namespace Shopsys\FrameworkBundle\Component\Router\FriendlyUrl;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Router\DomainRouterFactory;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\CompilerPass\FriendlyUrlDataProviderRegistry;
use Symfony\Component\Console\Output\OutputInterface;

class FriendlyUrlGeneratorFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\DomainRouterFactory
     */
    protected $domainRouterFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    protected $friendlyUrlFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\CompilerPass\FriendlyUrlDataProviderRegistry
     */
    protected $friendlyUrlDataProviderConfig;

    public function __construct(
        Domain $domain,
        DomainRouterFactory $domainRouterFactory,
        FriendlyUrlFacade $friendlyUrlFacade,
        FriendlyUrlDataProviderRegistry $friendlyUrlDataProviderConfig
    ) {
        $this->domain = $domain;
        $this->domainRouterFactory = $domainRouterFactory;
        $this->friendlyUrlFacade = $friendlyUrlFacade;
        $this->friendlyUrlDataProviderConfig = $friendlyUrlDataProviderConfig;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function generateUrlsForSupportedEntities(OutputInterface $output)
    {
        foreach ($this->domain->getAll() as $domainConfig) {
            $output->writeln(' Start of generating friendly urls for domain ' . $domainConfig->getUrl() . '');

            $countOfCreatedUrls = $this->generateUrlsByDomainConfig($output, $domainConfig);

            $output->writeln(sprintf(
                ' End of generating friendly urls for domain %s (%d).',
                $domainConfig->getUrl(),
                $countOfCreatedUrls
            ));
        }
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return int
     */
    protected function generateUrlsByDomainConfig(OutputInterface $output, DomainConfig $domainConfig)
    {
        $totalCountOfCreatedUrls = 0;
        $friendlyUrlRouter = $this->domainRouterFactory->getFriendlyUrlRouter($domainConfig);

        foreach ($friendlyUrlRouter->getRouteCollection() as $routeName => $route) {
            $countOfCreatedUrls = $this->generateUrlsByRoute($domainConfig, $routeName);
            $totalCountOfCreatedUrls += $countOfCreatedUrls;

            $output->writeln(sprintf(
                '   -> route %s in %s (%d)',
                $routeName,
                $route->getDefault('_controller'),
                $countOfCreatedUrls
            ));
        }

        return $totalCountOfCreatedUrls;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param string $routeName
     * @return int
     */
    protected function generateUrlsByRoute(DomainConfig $domainConfig, $routeName)
    {
        $countOfCreatedUrls = 0;

        $friendlyUrlsData = $this->friendlyUrlDataProviderConfig->getFriendlyUrlDataByRouteAndDomain($routeName, $domainConfig);

        foreach ($friendlyUrlsData as $friendlyUrlData) {
            $this->friendlyUrlFacade->createFriendlyUrlForDomain(
                $routeName,
                $friendlyUrlData->id,
                $friendlyUrlData->name,
                $domainConfig->getId()
            );
            $countOfCreatedUrls++;
        }

        return $countOfCreatedUrls;
    }
}
