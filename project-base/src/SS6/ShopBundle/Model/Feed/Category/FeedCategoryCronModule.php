<?php

namespace SS6\ShopBundle\Model\Feed\Category;

use SS6\ShopBundle\Component\Cron\CronModuleInterface;
use SS6\ShopBundle\Model\Feed\Category\FeedCategoryDownloadFacade;
use Symfony\Bridge\Monolog\Logger;

class FeedCategoryCronModule implements CronModuleInterface {

	/**
	 * @var \SS6\ShopBundle\Model\Feed\Category\FeedCategoryDownloadFacade
	 */
	private $feedCategoryDownloadFacade;

	/**
	 * @param \SS6\ShopBundle\Model\Feed\Category\FeedCategoryDownloadFacade FeedCategoryDownloadFacade
	 */
	public function __construct(FeedCategoryDownloadFacade $feedCategoryDownloadFacade) {
		$this->feedCategoryDownloadFacade = $feedCategoryDownloadFacade;
	}

	public function run(Logger $logger) {
		$this->feedCategoryDownloadFacade->download();
	}

}
