<?php

namespace Shopsys\FrameworkBundle\Model\Feed;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\Plugin\Cron\SimpleCronModuleInterface;
use Symfony\Bridge\Monolog\Logger;

class HourlyFeedCronModule implements SimpleCronModuleInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\FeedFacade
     */
    protected $feedFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    protected $logger;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Feed\FeedFacade $feedFacade
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(FeedFacade $feedFacade, Domain $domain)
    {
        $this->feedFacade = $feedFacade;
        $this->domain = $domain;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogger(Logger $logger): void
    {
        $this->logger = $logger;
    }

    public function run(): void
    {
        foreach ($this->feedFacade->getFeedsInfo('hourly') as $feedInfo) {
            foreach ($this->domain->getAll() as $domainConfig) {
                $startTime = microtime(true);
                $this->feedFacade->generateFeed($feedInfo->getName(), $domainConfig);
                $endTime = microtime(true);

                $this->logger->addDebug(sprintf(
                    'Feed "%s" generated on domain "%s" into "%s" in %.3f s',
                    $feedInfo->getName(),
                    $domainConfig->getName(),
                    $this->feedFacade->getFeedFilepath($feedInfo, $domainConfig),
                    $endTime - $startTime
                ));
            }
        }
    }
}
