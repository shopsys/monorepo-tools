<?php

namespace Tests\ShopBundle\Performance\Feed;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Router\CurrentDomainRouter;
use Shopsys\ShopBundle\Model\Feed\FeedConfig;
use Shopsys\ShopBundle\Model\Feed\FeedConfigFacade;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tests\ShopBundle\Performance\Feed\PerformanceResultsCsvExporter;
use Tests\ShopBundle\Performance\Feed\PerformanceTestSample;
use Tests\ShopBundle\Performance\JmeterCsvReporter;
use Tests\ShopBundle\Test\CrawlerTestCase;

class AllFeedsTest extends CrawlerTestCase
{
    const MAX_DURATION_FEED_SECONDS = 180;
    const MAX_DURATION_DELIVERY_FEED_SECONDS = 20;
    const SUSPICIOUSLY_LOW_DURATION_SECONDS = 5;

    const ROUTE_NAME_GENERATE_FEED = 'admin_feed_generate';
    const ADMIN_USERNAME = 'admin';
    const ADMIN_PASSWORD = 'admin123';

    public function testAllFeedsGeneration()
    {
        $consoleOutput = new ConsoleOutput();

        $consoleOutput->writeln('');
        $consoleOutput->writeln('<fg=cyan>Testing generation of all feeds:</fg=cyan>');

        $performanceTestSamples = [];
        $allFeedGenerationData = $this->getAllFeedGenerationData();
        foreach ($allFeedGenerationData as $feedGenerationData) {
            list($feedConfig, $domainConfig, $maxDuration) = $feedGenerationData;
            /* @var $feedConfig \Shopsys\ShopBundle\Model\Feed\FeedConfig */
            /* @var $domainConfig \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig */

            $consoleOutput->writeln(
                sprintf(
                    'Generating feed "%s" (%s) for %s (domain ID %d)...',
                    $feedConfig->getLabel(),
                    $feedConfig->getFeedName(),
                    $domainConfig->getName(),
                    $domainConfig->getId()
                )
            );

            $performanceTestSample = $this->doTestFeedGeneration($feedConfig, $domainConfig, $maxDuration);
            $consoleOutput->writeln($performanceTestSample->getMessage());

            $performanceTestSamples[] = $performanceTestSample;
        }

        $jmeterOutputFilename = $this->getContainer()->getParameter('shopsys.root_dir') . '/build/stats/performance-tests-feeds.csv';
        $this->exportJmeterCsvReport($performanceTestSamples, $jmeterOutputFilename);

        $this->assertSamplesAreSuccessful($performanceTestSamples);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Feed\FeedConfig $feedConfig
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param int $maxDuration
     * @return \Tests\ShopBundle\Performance\Feed\PerformanceTestSample
     */
    private function doTestFeedGeneration(FeedConfig $feedConfig, DomainConfig $domainConfig, $maxDuration)
    {
        $performanceTestSample = $this->generateFeed($feedConfig, $domainConfig);
        $this->setPerformanceTestSampleMessage($performanceTestSample, $maxDuration, $performanceTestSample->getDuration());

        return $performanceTestSample;
    }

    /**
     * @return array[]
     */
    public function getAllFeedGenerationData()
    {
        $feedConfigFacade = $this->getServiceByType(FeedConfigFacade::class);
        /* @var $feedConfigFacade \Shopsys\ShopBundle\Model\Feed\FeedConfigFacade */
        $domain = $this->getServiceByType(Domain::class);
        /* @var $domain \Shopsys\ShopBundle\Component\Domain\Domain */

        $feedGenerationData = $this->getFeedGenerationData(
            $feedConfigFacade->getFeedConfigs(),
            $domain->getAll(),
            self::MAX_DURATION_FEED_SECONDS
        );
        $deliveryFeedGenerationData = $this->getFeedGenerationData(
            $feedConfigFacade->getDeliveryFeedConfigs(),
            $domain->getAll(),
            self::MAX_DURATION_DELIVERY_FEED_SECONDS
        );

        return array_merge($feedGenerationData, $deliveryFeedGenerationData);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Feed\FeedConfig[] $feedConfigs
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig[] $domainConfigs
     * @param int $maxDuration
     * @return array[]
     */
    private function getFeedGenerationData(array $feedConfigs, array $domainConfigs, $maxDuration)
    {
        $feedGenerationData = [];
        foreach ($domainConfigs as $domainConfig) {
            foreach ($feedConfigs as $feedConfig) {
                $feedGenerationData[] = [$feedConfig, $domainConfig, $maxDuration];
            }
        }

        return $feedGenerationData;
    }

    /**
     * @param \Tests\ShopBundle\Performance\Feed\PerformanceTestSample $performanceTestSample
     * @param int $maxDuration
     * @param float $realDuration
     */
    private function setPerformanceTestSampleMessage(PerformanceTestSample $performanceTestSample, $maxDuration, $realDuration)
    {
        $minDuration = self::SUSPICIOUSLY_LOW_DURATION_SECONDS;

        if ($realDuration < $minDuration) {
            $message = sprintf('<fg=yellow>Feed generated in %.2F s, which is suspiciously fast.</fg=yellow>', $realDuration);
            $failMessage = sprintf('Feed was generated faster than in %d s, which is suspicious and should be checked.', $minDuration);
            $performanceTestSample->addFailMessage($failMessage);
        } elseif ($realDuration <= $maxDuration) {
            $message = sprintf('<fg=green>Feed generated in %.2F s.</fg=green>', $realDuration);
        } else {
            $message = sprintf('<fg=red>Feed generated in %.2F s, exceeding limit of %d s.</fg=red>', $realDuration, $maxDuration);
            $failMessage = sprintf('Feed generation exceeded limit of %d s.', $maxDuration);
            $performanceTestSample->addFailMessage($failMessage);
        }

        $performanceTestSample->setMessage($message);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Feed\FeedConfig $feedConfig
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Tests\ShopBundle\Performance\Feed\PerformanceTestSample
     */
    private function generateFeed(FeedConfig $feedConfig, DomainConfig $domainConfig)
    {
        $client = $this->getClient(true, self::ADMIN_USERNAME, self::ADMIN_PASSWORD);

        $router = $this->getServiceByType(CurrentDomainRouter::class);
        /* @var $router \Shopsys\ShopBundle\Component\Router\CurrentDomainRouter */

        $feedGenerationParameters = [
            'feedName' => $feedConfig->getFeedName(),
            'domainId' => $domainConfig->getId(),
        ];
        $uri = $router->generate(
            self::ROUTE_NAME_GENERATE_FEED,
            $feedGenerationParameters,
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $startTime = microtime(true);
        $this->makeRequestInTransaction($client, $uri);
        $endTime = microtime(true);

        $duration = $endTime - $startTime;
        $statusCode = $client->getResponse()->getStatusCode();

        $performanceTestSample = new PerformanceTestSample($feedConfig, $domainConfig, $uri, $duration, $statusCode);

        $expectedStatusCode = 302;
        if ($statusCode !== $expectedStatusCode) {
            $failMessage = sprintf('Admin request on %s ended with status code %d, expected %d.', $uri, $statusCode, $expectedStatusCode);
            $performanceTestSample->addFailMessage($failMessage);
        }

        return $performanceTestSample;
    }

    /**
     * @param \Tests\ShopBundle\Performance\Feed\PerformanceTestSample[] $performanceTestSamples
     */
    private function assertSamplesAreSuccessful(array $performanceTestSamples)
    {
        $failMessages = [];

        foreach ($performanceTestSamples as $performanceTestSample) {
            if (!$performanceTestSample->isSuccessful()) {
                $failMessages[] = sprintf(
                    'Generation of feed "%s" on domain with ID %d failed: %s',
                    $performanceTestSample->getFeedConfig()->getFeedName(),
                    $performanceTestSample->getDomainConfig()->getId(),
                    implode(' ', $performanceTestSample->getFailMessages())
                );
            }
            $this->addToAssertionCount(1);
        }

        if (count($failMessages) > 0) {
            $this->fail(implode("\n", $failMessages));
        }
    }

    /**
     * @param \Tests\ShopBundle\Performance\Feed\PerformanceTestSample[] $performanceTestSamples
     * @param string $jmeterOutputFilename
     */
    private function exportJmeterCsvReport(array $performanceTestSamples, $jmeterOutputFilename)
    {
        $jmeterCsvReporter = new JmeterCsvReporter();
        $performanceResultsCsvExporter = new PerformanceResultsCsvExporter($jmeterCsvReporter);

        $performanceResultsCsvExporter->exportJmeterCsvReport($performanceTestSamples, $jmeterOutputFilename);
    }
}
