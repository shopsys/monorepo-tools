<?php

namespace Tests\ShopBundle\Performance\Feed;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Feed\FeedConfigFacade;
use Shopsys\HttpSmokeTesting\Auth\BasicHttpAuth;
use Shopsys\ProductFeed\FeedConfigInterface;
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
            'environment' => 'test',
            'debug' => false,
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
            list($feedConfig, $domainConfig, $maxDuration) = $feedGenerationData;
            /* @var $feedConfig \Shopsys\ProductFeed\FeedConfigInterface */
            /* @var $domainConfig \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig */

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

        $this->exportJmeterCsvReport(
            $performanceTestSamples,
            self::$kernel->getContainer()->getParameter('shopsys.root_dir') . '/build/stats/performance-tests-feeds.csv'
        );

        $this->assertSamplesAreSuccessful($performanceTestSamples);
    }

    /**
     * @param \Shopsys\ProductFeed\FeedConfigInterface $feedConfig
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param int $maxDuration
     * @return \Tests\ShopBundle\Performance\Feed\PerformanceTestSample
     */
    private function doTestFeedGeneration(FeedConfigInterface $feedConfig, DomainConfig $domainConfig, $maxDuration)
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
        $feedConfigFacade = self::$kernel->getContainer()->get(FeedConfigFacade::class);
        /* @var $feedConfigFacade \Shopsys\FrameworkBundle\Model\Feed\FeedConfigFacade */
        $domain = self::$kernel->getContainer()->get(Domain::class);
        /* @var $domain \Shopsys\FrameworkBundle\Component\Domain\Domain */

        $feedGenerationData = $this->getFeedGenerationData(
            $feedConfigFacade->getStandardFeedConfigs(),
            $domain->getAll(),
            $this->maxDuration
        );
        $deliveryFeedGenerationData = $this->getFeedGenerationData(
            $feedConfigFacade->getDeliveryFeedConfigs(),
            $domain->getAll(),
            $this->deliveryMaxDuration
        );

        return array_merge($feedGenerationData, $deliveryFeedGenerationData);
    }

    /**
     * @param \Shopsys\ProductFeed\FeedConfigInterface[] $feedConfigs
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig[] $domainConfigs
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
     * @param \Shopsys\ProductFeed\FeedConfigInterface $feedConfig
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Tests\ShopBundle\Performance\Feed\PerformanceTestSample
     */
    private function generateFeed(FeedConfigInterface $feedConfig, DomainConfig $domainConfig)
    {
        $this->setUp();

        $router = self::$kernel->getContainer()->get('router');
        /* @var $router \Symfony\Component\Routing\RouterInterface */

        $uri = $router->generate(
            self::ROUTE_NAME_GENERATE_FEED,
            [
                'feedName' => $feedConfig->getFeedName(),
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
