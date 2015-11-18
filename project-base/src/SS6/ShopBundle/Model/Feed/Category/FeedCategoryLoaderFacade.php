<?php

namespace SS6\ShopBundle\Model\Feed\Category;

use SS6\ShopBundle\Model\Feed\Category\FeedCategoryFacade;
use SS6\ShopBundle\Model\Feed\Category\HeurekaFeedCategoryLoader;

class FeedCategoryLoaderFacade {

	/**
	 * @var string
	 */
	private $heurekaCategoryFeedUrl;

	/**
	 * @var string
	 */
	private $heurekaCategoryFeedBackupFilepath;

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
	 * @param string $heurekaCategoryFeedBackupFilepath
	 * @param \SS6\ShopBundle\Model\Feed\Category\FeedCategoryFacade $feedCategoryFacade
	 * @param \SS6\ShopBundle\Model\Feed\Category\HeurekaFeedCategoryLoader $heurekaFeedCategoryLoader
	 */
	public function __construct(
		$heurekaCategoryFeedUrl,
		$heurekaCategoryFeedBackupFilepath,
		FeedCategoryFacade $feedCategoryFacade,
		HeurekaFeedCategoryLoader $heurekaFeedCategoryLoader
	) {
		$this->heurekaCategoryFeedUrl = $heurekaCategoryFeedUrl;
		$this->heurekaCategoryFeedBackupFilepath = $heurekaCategoryFeedBackupFilepath;
		$this->feedCategoryFacade = $feedCategoryFacade;
		$this->heurekaFeedCategoryLoader = $heurekaFeedCategoryLoader;
	}

	public function download() {
		$feedCategoriesData = $this->heurekaFeedCategoryLoader->load($this->heurekaCategoryFeedUrl);
		$this->feedCategoryFacade->refreshFeedCategories($feedCategoriesData);
	}

	public function loadFromBackupFile() {
		$feedCategoriesData = $this->heurekaFeedCategoryLoader->load($this->heurekaCategoryFeedBackupFilepath);
		$this->feedCategoryFacade->refreshFeedCategories($feedCategoriesData);
	}

}
