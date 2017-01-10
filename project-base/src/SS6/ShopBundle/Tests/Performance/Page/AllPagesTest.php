<?php

namespace SS6\ShopBundle\Tests\Performance\Page;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Tests\Crawler\ResponseTest\UrlsProvider;
use SS6\ShopBundle\Tests\Performance\Page\PerformanceResultsCsvExporter;
use SS6\ShopBundle\Tests\Performance\Page\PerformanceTestSample;
use SS6\ShopBundle\Tests\Performance\Page\PerformanceTestSampleQualifier;
use SS6\ShopBundle\Tests\Performance\Page\PerformanceTestSamplesAggregator;
use SS6\ShopBundle\Tests\Performance\Page\PerformanceTestSummaryPrinter;
use SS6\ShopBundle\Tests\Test\FunctionalTestCase;
use Symfony\Component\Console\Output\ConsoleOutput;

class AllPagesTest extends FunctionalTestCase {

	const PASSES = 5;

	const ADMIN_USERNAME = 'superadmin';
	const ADMIN_PASSWORD = 'admin123';

	const FRONT_USERNAME = 'no-reply@netdevelo.cz';
	const FRONT_PASSWORD = 'user123';

	/**
	 * @group warmup
	 */
	public function testAdminPagesWarmup() {
		$urlsProvider = $this->getContainer()->get(UrlsProvider::class);
		/* @var $urlsProvider \SS6\ShopBundle\Tests\Crawler\ResponseTest\UrlsProvider */

		$this->doWarmupPagesWithProgress(
			$urlsProvider->getAdminTestableUrlsProviderData(),
			self::ADMIN_USERNAME,
			self::ADMIN_PASSWORD
		);
	}

	/**
	 * @group warmup
	 */
	public function testFrontPagesWarmup() {
		$urlsProvider = $this->getContainer()->get(UrlsProvider::class);
		/* @var $urlsProvider \SS6\ShopBundle\Tests\Crawler\ResponseTest\UrlsProvider */

		$this->doWarmupPagesWithProgress(
			$urlsProvider->getFrontTestableUrlsProviderData(),
			self::FRONT_USERNAME,
			self::FRONT_PASSWORD
		);
	}

	public function testAdminPages() {
		$urlsProvider = $this->getContainer()->get(UrlsProvider::class);
		/* @var $urlsProvider \SS6\ShopBundle\Tests\Crawler\ResponseTest\UrlsProvider */

		$this->doTestPagesWithProgress(
			$urlsProvider->getAdminTestableUrlsProviderData(),
			self::ADMIN_USERNAME,
			self::ADMIN_PASSWORD,
			$this->getContainer()->getParameter('ss6.root_dir') . '/build/stats/performance-tests-admin.csv'
		);
	}

	public function testFrontPages() {
		$urlsProvider = $this->getContainer()->get(UrlsProvider::class);
		/* @var $urlsProvider \SS6\ShopBundle\Tests\Crawler\ResponseTest\UrlsProvider */

		$this->doTestPagesWithProgress(
			$urlsProvider->getFrontTestableUrlsProviderData(),
			self::FRONT_USERNAME,
			self::FRONT_PASSWORD,
			$this->getContainer()->getParameter('ss6.root_dir') . '/build/stats/performance-tests-front.csv'
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
		$performanceTestSummaryPrinter = $this->getContainer()->get(PerformanceTestSummaryPrinter::class);
		/* @var $performanceTestSummaryPrinter \SS6\ShopBundle\Tests\Performance\Page\PerformanceTestSummaryPrinter */
		$performanceResultsCsvExporter = $this->getContainer()->get(PerformanceResultsCsvExporter::class);
		/* @var $performanceResultsCsvExporter \SS6\ShopBundle\Tests\Performance\Page\PerformanceResultsCsvExporter */
		$performanceTestSamplesAggregator = $this->getContainer()->get(PerformanceTestSamplesAggregator::class);
		/* @var $performanceTestSamplesAggregator \SS6\ShopBundle\Tests\Performance\Page\PerformanceTestSamplesAggregator */

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

		$performanceResultsCsvExporter->exportJmeterCsvReport($performanceTestSamples, $jmeterOutputFilename);

		$performanceTestSamplesAggregatedByUrl = $performanceTestSamplesAggregator
			->getPerformanceTestSamplesAggregatedByUrl($performanceTestSamples);

		$performanceTestSummaryPrinter->printSummary($performanceTestSamplesAggregatedByUrl, $consoleOutput);

		$this->doAssert($performanceTestSamplesAggregatedByUrl);
	}

	/**
	 * @param string $routeName
	 * @param string $url
	 * @param int $expectedStatusCode
	 * @param bool $asLogged
	 * @param string $username
	 * @param string $password
	 * @return \SS6\ShopBundle\Tests\Performance\Page\PerformanceTestSample
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
		$clientEntityManager = $client->getContainer()->get(EntityManager::class);
		/* @var $clientEntityManager \Doctrine\ORM\EntityManager */
		$urlsProvider = $this->getContainer()->get(UrlsProvider::class);
		/* @var $urlsProvider \SS6\ShopBundle\Tests\Crawler\ResponseTest\UrlsProvider */
		$urlWithCsrfToken = $urlsProvider->replaceCsrfTokensInUrl($url);

		$clientEntityManager->beginTransaction();

		$startTime = microtime(true);
		$client->request('GET', $urlWithCsrfToken);
		$endTime = microtime(true);

		$clientEntityManager->rollback();

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
	 * @param \SS6\ShopBundle\Tests\Performance\Page\PerformanceTestSample[] $performanceTestSamples
	 */
	private function doAssert(
		array $performanceTestSamples
	) {
		$performanceTestSampleQualifier = $this->getContainer()->get(PerformanceTestSampleQualifier::class);
		/* @var $performanceTestSampleQualifier \SS6\ShopBundle\Tests\Performance\Page\PerformanceTestSampleQualifier */

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

}
