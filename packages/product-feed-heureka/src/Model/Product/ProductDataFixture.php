<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\Product;

use Shopsys\Plugin\PluginDataFixtureInterface;
use Shopsys\Plugin\PluginDataStorageProviderInterface;

class ProductDataFixture implements PluginDataFixtureInterface
{
    const DOMAIN_ID_FIRST = 1;
    const DOMAIN_ID_SECOND = 2;
    const PRODUCT_ID_FIRST = 1;
    const PRODUCT_ID_SECOND = 2;
    const PRODUCT_ID_THIRD = 3;
    const PRODUCT_ID_FOURTH = 4;
    const PRODUCT_ID_FIFTH = 5;

    /**
     * @var \Shopsys\Plugin\PluginDataStorageProviderInterface
     */
    private $pluginDataStorageProvider;

    public function __construct(PluginDataStorageProviderInterface $pluginDataStorageProvider)
    {
        $this->pluginDataStorageProvider = $pluginDataStorageProvider;
    }

    public function load()
    {
        $productDataStorage = $this->getProductDataStorage();

        $productDataStorage->set(self::PRODUCT_ID_FIRST, [
            'cpc' => [
                self::DOMAIN_ID_FIRST => 12,
                self::DOMAIN_ID_SECOND => 5,
            ],
        ]);

        $productDataStorage->set(self::PRODUCT_ID_SECOND, [
            'cpc' => [
                self::DOMAIN_ID_FIRST => 3,
                self::DOMAIN_ID_SECOND => 2,
            ],
        ]);

        $productDataStorage->set(self::PRODUCT_ID_THIRD, [
            'cpc' => [
                self::DOMAIN_ID_FIRST => 1,
                self::DOMAIN_ID_SECOND => 1,
            ],
        ]);

        $productDataStorage->set(self::PRODUCT_ID_FOURTH, [
            'cpc' => [
                self::DOMAIN_ID_FIRST => 5,
                self::DOMAIN_ID_SECOND => 8,
            ],
        ]);

        $productDataStorage->set(self::PRODUCT_ID_FIFTH, [
            'cpc' => [
                self::DOMAIN_ID_FIRST => 10,
                self::DOMAIN_ID_SECOND => 5,
            ],
        ]);
    }

    /**
     * @return \Shopsys\Plugin\DataStorageInterface
     */
    private function getProductDataStorage()
    {
        return $this->pluginDataStorageProvider->getDataStorage(ShopsysProductFeedHeurekaBundle::class, 'product');
    }
}
