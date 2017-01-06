<?php

namespace SS6\ShopBundle\Tests\Performance\Feed;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Router\CurrentDomainRouter;
use SS6\ShopBundle\Model\Feed\FeedConfig;
use SS6\ShopBundle\Model\Feed\FeedConfigFacade;
use SS6\ShopBundle\Tests\Test\FunctionalTestCase;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Routing\RouterInterface;

class AllFeedsTest extends FunctionalTestCase {

	const MAX_DURATION_FEED_SECONDS = 180;
	const MAX_DURATION_DELIVERY_FEED_SECONDS = 20;
	const SUSPICIOUSLY_LOW_DURATION_SECONDS = 5;

	const ADMIN_USERNAME = 'admin';
	const ADMIN_PASSWORD = 'admin123';

	/**
	 * @param \SS6\ShopBundle\Model\Feed\FeedConfig $feedConfig
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @param int $maxDuration
	 * @dataProvider getAllFeedGenerationData
	 */
	public function testFeedGeneration(FeedConfig $feedConfig, DomainConfig $domainConfig, $maxDuration) {
		$consoleOutput = new ConsoleOutput();

		$consoleOutput->writeln('');
		$consoleOutput->writeln(
			sprintf(
				'Generating feed "%s" (%s) for %s (domain ID %d)...',
				$feedConfig->getLabel(),
				$feedConfig->getFeedName(),
				$domainConfig->getName(),
				$domainConfig->getId()
			)
		);

		$startTime = microtime(true);
		$this->generateFeed($feedConfig, $domainConfig);
		$endTime = microtime(true);

		$duration = $endTime - $startTime;
		$this->assertFeedGenerationDuration($maxDuration, $duration, $consoleOutput);
	}

	/**
	 * @return array[]
	 */
	public function getAllFeedGenerationData() {
		$feedConfigFacade = $this->getContainer()->get(FeedConfigFacade::class);
		/* @var $feedConfigFacade \SS6\ShopBundle\Model\Feed\FeedConfigFacade */
		$domain = $this->getContainer()->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Component\Domain\Domain */

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
	 * @param \SS6\ShopBundle\Model\Feed\FeedConfig[] $feedConfigs
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig[] $domainConfigs
	 * @param int $maxDuration
	 * @return array[]
	 */
	private function getFeedGenerationData(array $feedConfigs, array $domainConfigs, $maxDuration) {
		$feedGenerationData = [];
		foreach ($domainConfigs as $domainConfig) {
			foreach ($feedConfigs as $feedConfig) {
				$dataSetName = sprintf('feed "%s" on domain with ID %d', $feedConfig->getFeedName(), $domainConfig->getId());
				$feedGenerationData[$dataSetName] = [$feedConfig, $domainConfig, $maxDuration];
			}
		}

		return $feedGenerationData;
	}

	/**
	 * @param int $maxDuration
	 * @param float $realDuration
	 * @param \Symfony\Component\Console\Output\ConsoleOutput $consoleOutput
	 */
	private function assertFeedGenerationDuration($maxDuration, $realDuration, ConsoleOutput $consoleOutput) {
		$this->addToAssertionCount(1);

		if ($realDuration < self::SUSPICIOUSLY_LOW_DURATION_SECONDS) {
			$consoleOutput->writeln(
				sprintf('<fg=yellow>Feed generated in %.2F s, which is suspiciously fast and should be checked.</fg=yellow>', $realDuration)
			);
			$this->fail(sprintf('Feed was generated faster than in %d s, which is suspicious and should be checked.', $maxDuration));
		} elseif ($realDuration <= $maxDuration) {
			$consoleOutput->writeln(
				sprintf('<fg=green>Feed generated in %.2F s.</fg=green>', $realDuration)
			);
		} else {
			$consoleOutput->writeln(
				sprintf('<fg=red>Feed generated in %.2F s, exceeding limit of %d s.</fg=red>', $realDuration, $maxDuration)
			);
			$this->fail(sprintf('Feed generation exceeded limit of %d s.', $maxDuration));
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Feed\FeedConfig $feedConfig
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 */
	private function generateFeed(FeedConfig $feedConfig, DomainConfig $domainConfig) {
		$client = $this->getClient(true, self::ADMIN_USERNAME, self::ADMIN_PASSWORD);

		$router = $this->getContainer()->get(CurrentDomainRouter::class);
		/* @var $router \SS6\ShopBundle\Component\Router\CurrentDomainRouter */
		$clientEntityManager = $client->getContainer()->get(EntityManager::class);
		/* @var $clientEntityManager \Doctrine\ORM\EntityManager */

		$feedGenerationParameters = [
			'feedName' => $feedConfig->getFeedName(),
			'domainId' => $domainConfig->getId(),
		];
		$uri = $router->generate('admin_feed_generate', $feedGenerationParameters, RouterInterface::RELATIVE_PATH);

		$clientEntityManager->beginTransaction();
		$client->request('GET', $uri);
		$clientEntityManager->rollback();

		$expectedStatusCode = 302;
		$statusCode = $client->getResponse()->getStatusCode();
		if ($statusCode !== $expectedStatusCode) {
			$this->fail(sprintf('Admin request on %s failed with status code %d, expected %d.', $uri, $statusCode, $expectedStatusCode));
		}
	}

}
