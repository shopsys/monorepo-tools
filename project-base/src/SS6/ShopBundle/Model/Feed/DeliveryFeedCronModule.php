<?php

namespace SS6\ShopBundle\Model\Feed;

use SS6\ShopBundle\Component\Cron\CronModuleInterface;
use SS6\ShopBundle\Model\Feed\FeedFacade;
use Symfony\Bridge\Monolog\Logger;

class DeliveryFeedCronModule implements CronModuleInterface {

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedFacade
	 */
	private $feedFacade;

	public function __construct(FeedFacade $feedFacade) {
		$this->feedFacade = $feedFacade;
	}

	/**
	 * @inheritdoc
	 */
	public function setLogger(Logger $logger) {

	}

	public function run() {
		$this->feedFacade->generateDeliveryFeeds();
	}

}
