<?php

namespace SS6\ShopBundle\Tests\Performance\Feed;

use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Feed\FeedConfig;
use SS6\ShopBundle\Model\Feed\FeedConfigFacade;
use SS6\ShopBundle\Model\Feed\FeedFacade;
use SS6\ShopBundle\Tests\Test\FunctionalTestCase;
use Symfony\Component\Console\Output\ConsoleOutput;

class AllFeedsTest extends FunctionalTestCase {

	const MAX_DURATION_FEED_SECONDS = 180;
	const MAX_DURATION_DELIVERY_FEED_SECONDS = 20;

	/**
	 * @param \SS6\ShopBundle\Model\Feed\FeedConfig $feedConfig
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @param int $maxDuration
	 * @dataProvider getFeedGenerationData
	 */
	public function testFeedGeneration(FeedConfig $feedConfig, DomainConfig $domainConfig, $maxDuration) {
		$feedFacade = $this->getContainer()->get(FeedFacade::class);
		/* @var $feedFacade \SS6\ShopBundle\Model\Feed\FeedFacade */
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
		$feedFacade->generateFeed($feedConfig, $domainConfig);
		$endTime = microtime(true);

		$duration = $endTime - $startTime;
		$this->assertFeedGenerationDuration($maxDuration, $duration, $consoleOutput);
	}

	/**
	 * @return array[]
	 */
	public function getFeedGenerationData() {
		$feedConfigFacade = $this->getContainer()->get(FeedConfigFacade::class);
		/* @var $feedConfigFacade \SS6\ShopBundle\Model\Feed\FeedConfigFacade */
		$domain = $this->getContainer()->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Component\Domain\Domain */

		$feedGenerationData = [];
		foreach ($domain->getAll() as $domainConfig) {
			foreach ($feedConfigFacade->getFeedConfigs() as $feedConfig) {
				$feedGenerationData[] = [$feedConfig, $domainConfig, self::MAX_DURATION_FEED_SECONDS];
			}
			foreach ($feedConfigFacade->getDeliveryFeedConfigs() as $deliveryFeedConfig) {
				$feedGenerationData[] = [$deliveryFeedConfig, $domainConfig, self::MAX_DURATION_DELIVERY_FEED_SECONDS];
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

		if ($realDuration <= $maxDuration) {
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

}
