<?php

namespace Shopsys\ShopBundle\Model\Feed\Category;

use Shopsys\ShopBundle\Component\Cron\SimpleCronModuleInterface;
use Shopsys\ShopBundle\Model\Feed\Category\FeedCategoryLoaderFacade;
use Symfony\Bridge\Monolog\Logger;

class FeedCategoryCronModule implements SimpleCronModuleInterface
{
    /**
     * @var \Shopsys\ShopBundle\Model\Feed\Category\FeedCategoryLoaderFacade
     */
    private $feedCategoryLoaderFacade;

    /**
     * @param \Shopsys\ShopBundle\Model\Feed\Category\FeedCategoryLoaderFacade FeedCategoryDownloadFacade
     */
    public function __construct(FeedCategoryLoaderFacade $feedCategoryLoaderFacade)
    {
        $this->feedCategoryLoaderFacade = $feedCategoryLoaderFacade;
    }

    /**
     * @inheritdoc
     */
    public function setLogger(Logger $logger)
    {
    }

    public function run()
    {
        $this->feedCategoryLoaderFacade->download();
    }
}
