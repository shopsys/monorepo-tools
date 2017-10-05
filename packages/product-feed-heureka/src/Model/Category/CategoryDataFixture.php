<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\Category;

use Shopsys\Plugin\PluginDataFixtureInterface;
use Shopsys\ProductFeed\HeurekaBundle\DataStorageProvider;
use Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryDataFixture;

class CategoryDataFixture implements PluginDataFixtureInterface
{
    // category with ID 1 is usually hidden root category
    const CATEGORY_ID_FIRST = 2;
    const CATEGORY_ID_SECOND = 3;
    const CATEGORY_ID_THIRD = 4;
    const CATEGORY_ID_FOURTH = 5;
    const CATEGORY_ID_FIFTH = 6;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\DataStorageProvider
     */
    private $dataStorageProvider;

    /**
     * @param \Shopsys\ProductFeed\HeurekaBundle\DataStorageProvider $pluginDataStorageProvider
     */
    public function __construct(DataStorageProvider $pluginDataStorageProvider)
    {
        $this->dataStorageProvider = $pluginDataStorageProvider;
    }

    public function load()
    {
        $categoryDataStorage = $this->dataStorageProvider->getCategoryDataStorage();

        $categoryDataStorage->set(self::CATEGORY_ID_FIRST, [
            'heureka_category' => HeurekaCategoryDataFixture::HEUREKA_CATEGORY_ID_FIRST,
        ]);
        $categoryDataStorage->set(self::CATEGORY_ID_SECOND, [
            'heureka_category' => HeurekaCategoryDataFixture::HEUREKA_CATEGORY_ID_SECOND,
        ]);
        $categoryDataStorage->set(self::CATEGORY_ID_THIRD, [
            'heureka_category' => HeurekaCategoryDataFixture::HEUREKA_CATEGORY_ID_SECOND,
        ]);
        $categoryDataStorage->set(self::CATEGORY_ID_FOURTH, [
            'heureka_category' => HeurekaCategoryDataFixture::HEUREKA_CATEGORY_ID_THIRD,
        ]);
        $categoryDataStorage->set(self::CATEGORY_ID_FIFTH, [
            'heureka_category' => HeurekaCategoryDataFixture::HEUREKA_CATEGORY_ID_THIRD,
        ]);
    }
}
