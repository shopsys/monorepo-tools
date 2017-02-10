<?php

namespace Shopsys\ShopBundle\Model\Feed\Category;

use Shopsys\ShopBundle\Model\Feed\Category\FeedCategoryFacade;
use Shopsys\ShopBundle\Model\Feed\Category\HeurekaFeedCategoryLoader;

class FeedCategoryLoaderFacade
{

    /**
     * @var string
     */
    private $heurekaCategoryFeedUrl;

    /**
     * @var string
     */
    private $heurekaCategoryFeedBackupFilepath;

    /**
     * @var \Shopsys\ShopBundle\Model\Feed\Category\FeedCategoryFacade
     */
    private $feedCategoryFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Feed\Category\HeurekaFeedCategoryLoader
     */
    private $heurekaFeedCategoryLoader;

    /**
     * @param string $heurekaCategoryFeedUrl
     * @param string $heurekaCategoryFeedBackupFilepath
     * @param \Shopsys\ShopBundle\Model\Feed\Category\FeedCategoryFacade $feedCategoryFacade
     * @param \Shopsys\ShopBundle\Model\Feed\Category\HeurekaFeedCategoryLoader $heurekaFeedCategoryLoader
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
