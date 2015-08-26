<?php

namespace SS6\ShopBundle\Model\Feed\Category;

use SS6\ShopBundle\Component\Cron\CronServiceInterface;
use SS6\ShopBundle\Model\Feed\Category\FeedCategoryFacade;
use SS6\ShopBundle\Model\Feed\Category\HeurekaFeedCategoryLoader;
use Symfony\Bridge\Monolog\Logger;

class FeedCategoryCronService implements CronServiceInterface {

	/**
	 * @var string
	 */
	private $heurekaCategoryFeedUrl;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\Category\FeedCategoryFacade
	 */
	private $feedCategoryFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\Category\HeurekaFeedCategoryLoader
	 */
	private $heurekaFeedCategoryLoader;

	/**
	 * @param string $heurekaCategoryFeedUrl
	 * @param \SS6\ShopBundle\Model\Feed\Category\FeedCategoryFacade $feedCategoryFacade
	 * @param \SS6\ShopBundle\Model\Feed\Category\HeurekaFeedCategoryLoader $heurekaFeedCategoryLoader
	 */
	public function __construct(
		$heurekaCategoryFeedUrl,
		FeedCategoryFacade $feedCategoryFacade,
		HeurekaFeedCategoryLoader $heurekaFeedCategoryLoader
	) {
		$this->heurekaCategoryFeedUrl = $heurekaCategoryFeedUrl;
		$this->feedCategoryFacade = $feedCategoryFacade;
		$this->heurekaFeedCategoryLoader = $heurekaFeedCategoryLoader;
	}

	public function run(Logger $logger) {
		$feedCategoriesData = $this->heurekaFeedCategoryLoader->load($this->heurekaCategoryFeedUrl);
		$this->feedCategoryFacade->replaceFeedCategories($feedCategoriesData);
	}

}
