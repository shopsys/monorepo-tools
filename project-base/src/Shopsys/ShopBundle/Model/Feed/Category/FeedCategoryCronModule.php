<?php

namespace SS6\ShopBundle\Model\Feed\Category;

use SS6\ShopBundle\Component\Cron\CronModuleInterface;
use SS6\ShopBundle\Model\Feed\Category\FeedCategoryLoaderFacade;
use Symfony\Bridge\Monolog\Logger;

class FeedCategoryCronModule implements CronModuleInterface {

	/**
	 * @var \SS6\ShopBundle\Model\Feed\Category\FeedCategoryLoaderFacade
	 */
	private $feedCategoryLoaderFacade;

	/**
	 * @param \SS6\ShopBundle\Model\Feed\Category\FeedCategoryLoaderFacade FeedCategoryDownloadFacade
	 */
	public function __construct(FeedCategoryLoaderFacade $feedCategoryLoaderFacade) {
		$this->feedCategoryLoaderFacade = $feedCategoryLoaderFacade;
	}

	/**
	 * @inheritdoc
	 */
	public function setLogger(Logger $logger) {

	}

	public function run() {
		$this->feedCategoryLoaderFacade->download();
	}

}
