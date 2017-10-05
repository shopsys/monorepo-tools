<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory;

use Shopsys\Plugin\PluginDataFixtureInterface;
use Shopsys\ProductFeed\HeurekaBundle\DataStorageProvider;
use Shopsys\ProductFeed\HeurekaBundle\ShopsysProductFeedHeurekaBundle;

class HeurekaCategoryDataFixture implements PluginDataFixtureInterface
{
    const HEUREKA_CATEGORY_ID_FIRST = 1;
    const HEUREKA_CATEGORY_ID_SECOND = 2;
    const HEUREKA_CATEGORY_ID_THIRD = 3;

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
        $dataStorage = $this->getHeurekaCategoryDataStorage();

        $dataFixture = $this->getData();

        foreach ($dataFixture as $key => $data) {
            $dataStorage->set($key, $data);
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        $heurekaCategoryData = [];

        $heurekaCategoryData[self::HEUREKA_CATEGORY_ID_FIRST] = [
            'id' => self::HEUREKA_CATEGORY_ID_FIRST,
            'name' => 'Autobaterie',
            'full_name' => 'Heureka.cz | Auto-moto | Autodoplňky | Autobaterie',
        ];
        $heurekaCategoryData[self::HEUREKA_CATEGORY_ID_SECOND] = [
            'id' => self::HEUREKA_CATEGORY_ID_SECOND,
            'name' => 'Bublifuky',
            'full_name' => 'Heureka.cz | Dětské zboží | Hračky | Hry na zahradu | Bublifuky',
        ];
        $heurekaCategoryData[self::HEUREKA_CATEGORY_ID_THIRD] = [
            'id' => self::HEUREKA_CATEGORY_ID_THIRD,
            'name' => 'Cukřenky',
            'full_name' => 'Heureka.cz | Dům a zahrada | Domácnost | Kuchyně | Stolování | Cukřenky',
        ];

        return $heurekaCategoryData;
    }

    /**
     * @return \Shopsys\Plugin\DataStorageInterface
     */
    private function getHeurekaCategoryDataStorage()
    {
        return $this->dataStorageProvider->getHeurekaCategoryDataStorage();
    }
}
