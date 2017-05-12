<?php

namespace Tests\ShopBundle\Performance\Page;

use Symfony\Component\Console\Output\ConsoleOutput;
use Tests\ShopBundle\Performance\JmeterCsvReporter;
use Tests\ShopBundle\Performance\Page\PerformanceResultsCsvExporter;
use Tests\ShopBundle\Performance\Page\PerformanceTestSample;
use Tests\ShopBundle\Performance\Page\PerformanceTestSampleQualifier;
use Tests\ShopBundle\Performance\Page\PerformanceTestSamplesAggregator;
use Tests\ShopBundle\Performance\Page\PerformanceTestSummaryPrinter;
use Tests\ShopBundle\Smoke\Http\HttpSmokeTestCase;

class AllPagesTest extends HttpSmokeTestCase
{
    const PASSES = 5;

    const ADMIN_USERNAME = 'superadmin';
    const ADMIN_PASSWORD = 'admin123';

    const FRONT_USERNAME = 'no-reply@netdevelo.cz';
    const FRONT_PASSWORD = 'user123';

    /**
     * @group warmup
     */
    public function testAdminPagesWarmup()
    {
        $this->doWarmupPagesWithProgress(
            $this->createUrlsProvider()->getAdminTestableUrlsProviderData(),
            self::ADMIN_USERNAME,
            self::ADMIN_PASSWORD
        );
    }

    /**
     * @group warmup
     */
    public function testFrontPagesWarmup()
    {
        $this->doWarmupPagesWithProgress(
            $this->createUrlsProvider()->getFrontTestableUrlsProviderData(),
            self::FRONT_USERNAME,
            self::FRONT_PASSWORD
        );
    }

    public function testAdminPages()
    {
        $this->doTestPagesWithProgress(
            $this->createUrlsProvider()->getAdminTestableUrlsProviderData(),
            self::ADMIN_USERNAME,
            self::ADMIN_PASSWORD,
            $this->getContainer()->getParameter('shopsys.root_dir') . '/build/stats/performance-tests-admin.csv'
        );
    }

    public function testFrontPages()
    {
        $this->doTestPagesWithProgress(
            $this->createUrlsProvider()->getFrontTestableUrlsProviderData(),
            self::FRONT_USERNAME,
            self::FRONT_PASSWORD,
            $this->getContainer()->getParameter('shopsys.root_dir') . '/build/stats/performance-tests-front.csv'
        );
    }

    /**
     * @param array $testableUrlsDataProviderData
     * @param string $username
     * @param string $password
     */
    private function doWarmupPagesWithProgress(
        array $testableUrlsDataProviderData,
        $username,
        $password
    ) {
        $consoleOutput = new ConsoleOutput();
        $consoleOutput->writeln('');

        $countTestedUrls = count($testableUrlsDataProviderData);
        $pageIndex = 0;
        foreach ($testableUrlsDataProviderData as $testUrlData) {
            $pageIndex++;
            list($routeName, $url, $expectedStatusCode, $asLogged) = $testUrlData;

            $progressLine = sprintf(
                'Warmup: %3d%% (%s)',
                round($pageIndex / $countTestedUrls * 100),
                $routeName
            );
            $consoleOutput->write(str_pad($progressLine, 80) . "\r");

            $this->doTestUrl(
                $routeName,
                $url,
                $expectedStatusCode,
                $asLogged,
                $username,
                $password
            );
        }
    }

    /**
     * @param array $testableUrlsDataProviderData
     * @param string $username
     * @param string $password
     * @param string $jmeterOutputFilename
     */
    private function doTestPagesWithProgress(
        array $testableUrlsDataProviderData,
        $username,
        $password,
        $jmeterOutputFilename
    ) {
        $consoleOutput = new ConsoleOutput();
        $consoleOutput->writeln('');

        $performanceTestSamples = [];

        $countTestedUrls = count($testableUrlsDataProviderData);
        for ($pass = 1; $pass <= self::PASSES; $pass++) {
            $pageIndex = 0;
            foreach ($testableUrlsDataProviderData as $testUrlData) {
                $pageIndex++;
                list($routeName, $url, $expectedStatusCode, $asLogged) = $testUrlData;

                $progressLine = sprintf(
                    '%s: %3d%% (%s)',
                    'Pass ' . $pass . '/' . self::PASSES,
                    round($pageIndex / $countTestedUrls * 100),
                    $url
                );
                $consoleOutput->write(str_pad($progressLine, 80) . "\r");

                $performanceTestSamples[] = $this->doTestUrl(
                    $routeName,
                    $url,
                    $expectedStatusCode,
                    $asLogged,
                    $username,
                    $password
                );
            }
        }

        $performanceTestSamplesAggregatedByUrl = $this->aggregatePerformanceTestSamplesByUrl($performanceTestSamples);
        $this->exportJmeterCsvReport($performanceTestSamples, $jmeterOutputFilename);
        $this->printPerformanceTestsSummary($performanceTestSamplesAggregatedByUrl, $consoleOutput);

        $this->doAssert($performanceTestSamplesAggregatedByUrl);
    }

    /**
     * @param string $routeName
     * @param string $url
     * @param int $expectedStatusCode
     * @param bool $asLogged
     * @param string $username
     * @param string $password
     * @return \Tests\ShopBundle\Performance\Page\PerformanceTestSample
     */
    private function doTestUrl(
        $routeName,
        $url,
        $expectedStatusCode,
        $asLogged,
        $username,
        $password
    ) {
        if ($asLogged) {
            $client = $this->getClient(true, $username, $password);
        } else {
            $client = $this->getClient(true);
        }

        $urlWithCsrfToken = $this->createUrlsProvider()->replaceCsrfTokensInUrl($url);

        $startTime = microtime(true);
        $this->makeRequestInTransaction($client, $urlWithCsrfToken);
        $endTime = microtime(true);

        $statusCode = $client->getResponse()->getStatusCode();

        return new PerformanceTestSample(
            $routeName,
            $url,
            ($endTime - $startTime) * 1000,
            0, // Currently, we are not able to measure query count
            $statusCode,
            $statusCode === $expectedStatusCode
        );
    }

    /**
     * @param \Tests\ShopBundle\Performance\Page\PerformanceTestSample[] $performanceTestSamples
     */
    private function doAssert(
        array $performanceTestSamples
    ) {
        $performanceTestSampleQualifier = new PerformanceTestSampleQualifier();

        $overallStatus = $performanceTestSampleQualifier->getOverallStatus($performanceTestSamples);

        switch ($overallStatus) {
            case PerformanceTestSampleQualifier::STATUS_OK:
            case PerformanceTestSampleQualifier::STATUS_WARNING:
                $this->assertTrue(true);
                return;
            case PerformanceTestSampleQualifier::STATUS_CRITICAL:
            default:
                $this->fail('Values are above critical threshold');
                return;
        }
    }

    /**
     * @param \Tests\ShopBundle\Performance\Page\PerformanceTestSample[] $performanceTestSamples
     * @param string $jmeterOutputFilename
     */
    private function exportJmeterCsvReport(array $performanceTestSamples, $jmeterOutputFilename)
    {
        $jmeterCsvReporter = new JmeterCsvReporter();
        $performanceResultsCsvExporter = new PerformanceResultsCsvExporter($jmeterCsvReporter);

        $performanceResultsCsvExporter->exportJmeterCsvReport($performanceTestSamples, $jmeterOutputFilename);
    }

    /**
     * @param \Tests\ShopBundle\Performance\Page\PerformanceTestSample[] $performanceTestSamples
     * @return \Tests\ShopBundle\Performance\Page\PerformanceTestSample[]
     */
    private function aggregatePerformanceTestSamplesByUrl(array $performanceTestSamples)
    {
        $performanceTestSamplesAggregator = new PerformanceTestSamplesAggregator();

        return $performanceTestSamplesAggregator->getPerformanceTestSamplesAggregatedByUrl($performanceTestSamples);
    }

    /**
     * @param \Tests\ShopBundle\Performance\Page\PerformanceTestSample[] $performanceTestSamples
     * @param \Symfony\Component\Console\Output\ConsoleOutput $consoleOutput
     */
    private function printPerformanceTestsSummary(array $performanceTestSamples, ConsoleOutput $consoleOutput)
    {
        $performanceTestSampleQualifier = new PerformanceTestSampleQualifier();
        $performanceTestSummaryPrinter = new PerformanceTestSummaryPrinter($performanceTestSampleQualifier);

        $performanceTestSummaryPrinter->printSummary($performanceTestSamples, $consoleOutput);
    }
}
