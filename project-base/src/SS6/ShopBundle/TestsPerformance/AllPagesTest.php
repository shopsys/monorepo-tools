<?php

namespace SS6\ShopBundle\TestsPerformance;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\DataFixture\PersistentReferenceService;
use SS6\ShopBundle\Component\Test\FunctionalTestCase;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\TestsCrawler\ResponseTest\UrlsProvider;
use SS6\ShopBundle\TestsPerformance\PagePerformanceResultsCollection;
use SS6\ShopBundle\TestsPerformance\ThresholdService;
use Symfony\Component\Console\Output\ConsoleOutput;

class AllPagesTest extends FunctionalTestCase {

	const PASSES = 3;

	public function testAdminPages() {
		$this->doTestPagesWithProgress(
			$this->createUrlProvider()->getAdminTestableUrlsProviderData(),
			'admin',
			'admin123'
		);
	}

	public function testFrontPages() {
		$this->doTestPagesWithProgress($this->createUrlProvider()->getFrontTestableUrlsProviderData(), 'no-reply@netdevelo.cz', 'user123');
	}

	/**
	 * @return \SS6\ShopBundle\TestsCrawler\ResponseTest\UrlsProvider
	 */
	private function createUrlProvider() {
		$domain = $this->getContainer()->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */
		$router = $this->getContainer()->get('router');
		/* @var $router \Symfony\Component\Routing\RouterInterface */
		$persistentReferenceService = $this->getContainer()->get(PersistentReferenceService::class);
		/* @var $router \SS6\ShopBundle\Component\DataFixture\PersistentReferenceService */

		// DataProvider is called before setUp() - domain is not set
		$domain->switchDomainById(1);
		return new UrlsProvider($persistentReferenceService, $router);
	}

	/**
	 * @param array $testableUrlsDataProviderData
	 * @param string $username
	 * @param string $password
	 */
	private function doTestPagesWithProgress(array $testableUrlsDataProviderData, $username, $password) {
		$consoleOutput = new ConsoleOutput();
		$thresholdService = new ThresholdService();
		$pagePerformanceResultsCollection = new PagePerformanceResultsCollection();
		$consoleOutput->writeln('');

		$countTestedUrls = count($testableUrlsDataProviderData);
		for ($pass = 0; $pass <= self::PASSES; $pass++) {
			$pageIndex = 0;
			foreach ($testableUrlsDataProviderData as $testUrlData) {
				$pageIndex++;
				list($routeName, $url, $dummyExpectedStatusCode, $asLogged) = $testUrlData;

				$progressLine = sprintf(
					"%s: %3d%% (%s)",
					$pass === 0 ? 'Warmup' : 'Pass ' . $pass . '/' . self::PASSES,
					round($pageIndex / $countTestedUrls * 100),
					$routeName
				);
				$consoleOutput->write(str_pad($progressLine, 80) . "\r");

				$this->doTestUrl($pagePerformanceResultsCollection, $routeName, $url, $asLogged, $username, $password);
			}

			if ($pass === 0) {
				$pagePerformanceResultsCollection->clear();
			}
		}

		$this->printSummary($pagePerformanceResultsCollection, $thresholdService, $consoleOutput);
		$this->doAssert($pagePerformanceResultsCollection, $thresholdService);
	}

	/**
	 * @param \SS6\ShopBundle\TestsPerformance\PagePerformanceResultsCollection $pagePerformanceResultsCollection
	 * @param string $routeName
	 * @param string $url
	 * @param bool $asLogged
	 * @param string $username
	 * @param string $password
	 */
	private function doTestUrl(
		PagePerformanceResultsCollection $pagePerformanceResultsCollection,
		$routeName,
		$url,
		$asLogged,
		$username,
		$password
	) {
		if ($asLogged) {
			$client = $this->getClient(true, $username, $password);
		} else {
			$client = $this->getClient(true);
		}
		$em = $client->getContainer()->get(EntityManager::class);
		/* @var $em \Doctrine\ORM\EntityManager */

		$client->enableProfiler();
		$em->beginTransaction();
		$client->request('GET', $url);
		$em->rollback();

		$profile = $client->getProfile();

		$timeCollector = $profile->getCollector('time');
		/* @var $timeCollector \Symfony\Component\HttpKernel\DataCollector\TimeDataCollector */
		$dbCollector = $profile->getCollector('db');
		/* @var	$dbCollector \Symfony\Bridge\Doctrine\DataCollector\DoctrineDataCollector */

		$pagePerformanceResultsCollection->addMeasurement(
			$routeName,
			$url,
			$timeCollector->getDuration(),
			$dbCollector->getQueryCount()
		);
	}

	/**
	 * @param \SS6\ShopBundle\TestsPerformance\PagePerformanceResultsCollection $pagePerformanceResultsCollection
	 * @param \SS6\ShopBundle\TestsPerformance\ThresholdService $thresholdService
	 * @param \Symfony\Component\Console\Output\ConsoleOutput $consoleOutput
	 */
	private function printSummary(
		PagePerformanceResultsCollection $pagePerformanceResultsCollection,
		ThresholdService $thresholdService,
		ConsoleOutput $consoleOutput
	) {
		foreach ($pagePerformanceResultsCollection->getAll() as $pagePerformanceResult) {
			$consoleOutput->writeln('');
			$consoleOutput->writeln(
				'Route name: ' . $pagePerformanceResult->getRouteName() . ' (' . $pagePerformanceResult->getUrl() . ')'
			);
			$tag = $thresholdService->getDurationFormatterTag($pagePerformanceResult->getAvgDuration());
			$consoleOutput->writeln('<' . $tag . '>Avg duration: ' . $pagePerformanceResult->getAvgDuration() . 'ms</' . $tag . '>');
			$tag = $thresholdService->getQueryCountFormatterTag($pagePerformanceResult->getMaxQueryCount());
			$consoleOutput->writeln('<' . $tag . '>Max query count: ' . $pagePerformanceResult->getMaxQueryCount() . '</' . $tag . '>');
		}

		$resultStatus = $thresholdService->getPagePerformanceCollectionStatus($pagePerformanceResultsCollection);
		$resultColor = $thresholdService->getStatusConsoleTextColor($resultStatus);
		$resultTag = 'fg=' . $resultColor;
		$consoleOutput->writeln('');
		switch ($resultStatus) {
			case ThresholdService::STATUS_OK:
				$consoleOutput->write('<' . $resultTag . '>Test passed</' . $resultTag . '>');
				return;
			case ThresholdService::STATUS_WARNING:
				$consoleOutput->write('<' . $resultTag . '>Test passed, but contains some warnings</' . $resultTag . '>');
				return;
			case ThresholdService::STATUS_CRITICAL:
			default:
				$consoleOutput->write('<' . $resultTag . '>Test failed</' . $resultTag . '>');
				return;
		}
	}

	/**
	 * @param \SS6\ShopBundle\TestsPerformance\PagePerformanceResultsCollection $pagePerformanceResultsCollection
	 * @param \SS6\ShopBundle\TestsPerformance\ThresholdService $thresholdService
	 */
	private function doAssert(
		PagePerformanceResultsCollection $pagePerformanceResultsCollection,
		ThresholdService $thresholdService
	) {
		$resultStatus = $thresholdService->getPagePerformanceCollectionStatus($pagePerformanceResultsCollection);

		switch ($resultStatus) {
			case ThresholdService::STATUS_OK:
			case ThresholdService::STATUS_WARNING:
				$this->assertTrue(true);
				return;
			case ThresholdService::STATUS_CRITICAL:
			default:
				$this->fail('Values are above critical threshold');
				return;
		}
	}

}
