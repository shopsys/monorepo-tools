<?php

namespace SS6\ShopBundle\Model\Feed\Category;

use SS6\ShopBundle\Component\Cron\CronServiceInterface;
use SS6\ShopBundle\Model\Feed\Category\FeedCategoryFacade;
use SS6\ShopBundle\Model\Feed\Category\HeurekaFeedCategoryLoader;
use Symfony\Bridge\Monolog\Logger;

class FeedCategoryCronService implements CronServiceInterface {

	/**
	 * @var \SS6\ShopBundle\Model\Feed\Category\FeedCategoryFacade
	 */
	private $feedCategoryFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\Category\HeurekaFeedCategoryLoader
	 */
	private $heurekaFeedCategoryLoader;

	public function __construct(
		FeedCategoryFacade $feedCategoryFacade,
		HeurekaFeedCategoryLoader $heurekaFeedCategoryLoader
	) {
		$this->feedCategoryFacade = $feedCategoryFacade;
		$this->heurekaFeedCategoryLoader = $heurekaFeedCategoryLoader;
	}

	public function run(Logger $logger) {
		$feedCategoriesData = $this->heurekaFeedCategoryLoader
			->load('http://www.heureka.cz/direct/xml-export/shops/heureka-sekce.xml');

		$this->feedCategoryFacade->replaceFeedCategories($feedCategoriesData);
	}

}
