<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory;

use Shopsys\Plugin\Cron\SimpleCronModuleInterface;
use Symfony\Bridge\Monolog\Logger;

class HeurekaCategoryCronModule implements SimpleCronModuleInterface
{
    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryDownloader
     */
    private $heurekaCategoryDownloader;

    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    private $logger;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade
     */
    private $heurekaCategoryFacade;

    /**
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryDownloader $heurekaCategoryDownloader
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade $heurekaCategoryFacade
     */
    public function __construct(
        HeurekaCategoryDownloader $heurekaCategoryDownloader,
        HeurekaCategoryFacade $heurekaCategoryFacade
    ) {
        $this->heurekaCategoryDownloader = $heurekaCategoryDownloader;
        $this->heurekaCategoryFacade = $heurekaCategoryFacade;
    }

    /**
     * @inheritdoc
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        try {
            $heurekaCategoriesData = $this->heurekaCategoryDownloader->getHeurekaCategories();
            $this->heurekaCategoryFacade->saveHeurekaCategories($heurekaCategoriesData);
        } catch (\Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryDownloadFailedException $e) {
            $this->logger->addError($e->getMessage());
        }
    }
}
