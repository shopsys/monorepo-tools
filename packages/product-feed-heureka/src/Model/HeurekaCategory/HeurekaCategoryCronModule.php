<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory;

use Shopsys\Plugin\Cron\SimpleCronModuleInterface;
use Shopsys\ProductFeed\HeurekaBundle\DataStorageProvider;
use Symfony\Bridge\Monolog\Logger;

class HeurekaCategoryCronModule implements SimpleCronModuleInterface
{
    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryDownloader
     */
    private $heurekaCategoryDownloader;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\DataStorageProvider
     */
    private $dataStorageProvider;

    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    private $logger;

    public function __construct(
        HeurekaCategoryDownloader $heurekaCategoryDownloader,
        DataStorageProvider $dataStorageProvider
    ) {
        $this->heurekaCategoryDownloader = $heurekaCategoryDownloader;
        $this->dataStorageProvider = $dataStorageProvider;
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
            $newCategories = $this->heurekaCategoryDownloader->getHeurekaCategories();
            $this->saveHeurekaCategories($newCategories);
        } catch (\Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryDownloadFailedException $e) {
            $this->logger->addError($e->getMessage());
        }
    }

    /**
     * @param array[] $newCategories
     */
    private function saveHeurekaCategories(array $newCategories)
    {
        $heurekaCategoryDataStorage = $this->dataStorageProvider->getHeurekaCategoryDataStorage();
        $existingCategoryIds = array_keys($heurekaCategoryDataStorage->getAll());

        foreach ($newCategories as $id => $category) {
            $heurekaCategoryDataStorage->set($id, $category);
        }

        $newCategoryIds = array_keys($newCategories);
        $categoryIdsToDelete = array_diff($existingCategoryIds, $newCategoryIds);
        foreach ($categoryIdsToDelete as $categoryIdToDelete) {
            $heurekaCategoryDataStorage->remove($categoryIdToDelete);
        }

        $this->logger->addInfo(sprintf(
            'Downloaded %d categories (%d old categories deleted).',
            count($newCategoryIds),
            count($categoryIdsToDelete)
        ));
    }
}
