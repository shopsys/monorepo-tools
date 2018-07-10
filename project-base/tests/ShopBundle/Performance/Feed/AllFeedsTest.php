<?php

namespace Tests\ShopBundle\Performance\Feed;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Environment\EnvironmentType;
use Shopsys\FrameworkBundle\Model\Feed\FeedInfoInterface;
use Shopsys\FrameworkBundle\Model\Feed\FeedRegistry;
use Shopsys\HttpSmokeTesting\Auth\BasicHttpAuth;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpFoundation\Request;
use Tests\ShopBundle\Performance\JmeterCsvReporter;

class AllFeedsTest extends KernelTestCase
{
    const ROUTE_NAME_GENERATE_FEED = 'admin_feed_generate';
    const ADMIN_USERNAME = 'admin';
    const ADMIN_PASSWORD = 'admin123';

    /**
     * @var int
     */
    private $maxDuration;

    /**
     * @var int
     */
    private $deliveryMaxDuration;

    /**
     * @var int
     */
    private $minDuration;

    protected function setUp()
    {
        parent::setUp();

        static::bootKernel([
            'environment' => EnvironmentType::TEST,
            'debug' => EnvironmentType::isDebug(EnvironmentType::TEST),
        ]);

        $container = self::$kernel->getContainer();
        $container->get(Domain::class)
            ->switchDomainById(1);

        $this->maxDuration = $container->getParameter('shopsys.performance_test.feed.max_duration_seconds');
        $this->deliveryMaxDuration = $container->getParameter('shopsys.performance_test.feed.delivery.max_duration_seconds');
        $this->minDuration = $container->getParameter('shopsys.performance_test.feed.min_duration_seconds');
    }

    public function testAllFeedsGeneration()
    {
        $consoleOutput = new ConsoleOutput();

        $consoleOutput->writeln('');
        $consoleOutput->writeln('<fg=cyan>Testing generation of all feeds:</fg=cyan>');

        $performanceTestSamples = [];
        $allFeedGenerationData = $this->getAllFeedGenerationData();
        foreach ($allFeedGenerationData as $feedGenerationData) {
            list($feedInfo, $domainConfig, $maxDuration) = $feedGenerationData;
            /* @var $feedInfo \Shopsys\FrameworkBundle\Model\Feed\FeedInfoInterface */
            /* @var $domainConfig \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig */

            $consoleOutput->writeln(
                sprintf(
                    'Generating feed "%s" (%s) for %s (domain ID %d)...',
                    $feedInfo->getLabel(),
                    $feedInfo->getName(),
                    $domainConfig->getName(),
                    $domainConfig->getId()
                )
            );

            $performanceTestSample = $this->doTestFeedGeneration($feedInfo, $domainConfig, $maxDuration);
            $consoleOutput->writeln($performanceTestSample->getMessage());

            $performanceTestSamples[] = $performanceTestSample;
        }

        $this->exportJmeterCsvReport(
            $performanceTestSamples,
            self::$kernel->getContainer()->getParameter('shopsys.root_dir') . '/build/stats/performance-tests-feeds.csv'
        );

        $this->assertSamplesAreSuccessful($performanceTestSamples);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Feed\FeedInfoInterface $feedInfo
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param int $maxDuration
     * @return \Tests\ShopBundle\Performance\Feed\PerformanceTestSample
     */
    private function doTestFeedGeneration(FeedInfoInterface $feedInfo, DomainConfig $domainConfig, $maxDuration)
    {
        $performanceTestSample = $this->generateFeed($feedInfo, $domainConfig);
        $this->setPerformanceTestSampleMessage($performanceTestSample, $maxDuration, $performanceTestSample->getDuration());

        return $performanceTestSample;
    }

    /**
     * @return array[]
     */
    public function getAllFeedGenerationData()
    {
        $feedRegistry = self::$kernel->getContainer()->get(FeedRegistry::class);
        /* @var $feedRegistry \Shopsys\FrameworkBundle\Model\Feed\FeedRegistry */
        $domain = self::$kernel->getContainer()->get(Domain::class);
        /* @var $domain \Shopsys\FrameworkBundle\Component\Domain\Domain */

        $dailyFeedGenerationData = $this->getFeedGenerationData(
            $feedRegistry->getFeeds('daily'),
            $domain->getAll(),
            $this->maxDuration
        );
        $hourlyFeedGenerationData = $this->getFeedGenerationData(
            $feedRegistry->getFeeds('hourly'),
            $domain->getAll(),
            $this->deliveryMaxDuration
        );

        return array_merge($dailyFeedGenerationData, $hourlyFeedGenerationData);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Feed\FeedInterface[] $feeds
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig[] $domainConfigs
     * @param int $maxDuration
     * @return array[]
     */
    private function getFeedGenerationData(array $feeds, array $domainConfigs, $maxDuration)
    {
        $feedGenerationData = [];
        foreach ($domainConfigs as $domainConfig) {
            foreach ($feeds as $feed) {
                $feedGenerationData[] = [$feed->getInfo(), $domainConfig, $maxDuration];
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
        $minDuration = $this->minDuration;

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
     * @param \Shopsys\FrameworkBundle\Model\Feed\FeedInfoInterface $feed
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Tests\ShopBundle\Performance\Feed\PerformanceTestSample
     */
    private function generateFeed(FeedInfoInterface $feed, DomainConfig $domainConfig)
    {
        $this->setUp();

        $router = self::$kernel->getContainer()->get('router');
        /* @var $router \Symfony\Component\Routing\RouterInterface */

        $uri = $router->generate(
            self::ROUTE_NAME_GENERATE_FEED,
            [
                'feedName' => $feed->getName(),
                'domainId' => $domainConfig->getId(),
            ]
        );
        $request = Request::create($uri);
        $auth = new BasicHttpAuth(self::ADMIN_USERNAME, self::ADMIN_PASSWORD);
        $auth->authenticateRequest($request);

        $entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        /* @var $entityManager \Doctrine\ORM\EntityManager */

        $startTime = microtime(true);
        $entityManager->beginTransaction();
        $response = static::$kernel->handle($request);
        $entityManager->rollback();
        $endTime = microtime(true);

        $duration = $endTime - $startTime;
        $statusCode = $response->getStatusCode();

        $performanceTestSample = new PerformanceTestSample($feed, $domainConfig, $uri, $duration, $statusCode);

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
                    $performanceTestSample->getFeedName(),
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
