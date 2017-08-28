<?php

namespace Shopsys\ProductFeed\ZboziBundle;

use Shopsys\Plugin\PluginDataFixtureInterface;
use Shopsys\Plugin\PluginDataStorageProviderInterface;

class ZboziPluginDataFixture implements PluginDataFixtureInterface
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

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function load()
    {
        $productDataStorage = $this->getProductDataStorage();

        $productDataStorage->set(self::PRODUCT_ID_FIRST, [
            'cpc' => [
                self::DOMAIN_ID_FIRST => 15,
                self::DOMAIN_ID_SECOND => 12,
            ],
            'cpc_search' => [
                self::DOMAIN_ID_FIRST => 8,
                self::DOMAIN_ID_SECOND => 15,
            ],
            'show' => [
                self::DOMAIN_ID_FIRST => true,
                self::DOMAIN_ID_SECOND => true,
            ],
        ]);

        $productDataStorage->set(self::PRODUCT_ID_SECOND, [
            'cpc' => [
                self::DOMAIN_ID_FIRST => 5,
                self::DOMAIN_ID_SECOND => 20,
            ],
            'cpc_search' => [
                self::DOMAIN_ID_FIRST => 3,
                self::DOMAIN_ID_SECOND => 5,
            ],
            'show' => [
                self::DOMAIN_ID_FIRST => false,
                self::DOMAIN_ID_SECOND => true,
            ],
        ]);

        $productDataStorage->set(self::PRODUCT_ID_THIRD, [
            'cpc' => [
                self::DOMAIN_ID_FIRST => 10,
                self::DOMAIN_ID_SECOND => 15,
            ],
            'cpc_search' => [
                self::DOMAIN_ID_FIRST => 5,
                self::DOMAIN_ID_SECOND => 7,
            ],
            'show' => [
                self::DOMAIN_ID_FIRST => false,
                self::DOMAIN_ID_SECOND => false,
            ],
        ]);

        $productDataStorage->set(self::PRODUCT_ID_FOURTH, [
            'cpc' => [
                self::DOMAIN_ID_FIRST => 9,
                self::DOMAIN_ID_SECOND => 4,
            ],
            'cpc_search' => [
                self::DOMAIN_ID_FIRST => 8,
                self::DOMAIN_ID_SECOND => 3,
            ],
            'show' => [
                self::DOMAIN_ID_FIRST => true,
                self::DOMAIN_ID_SECOND => true,
            ],
        ]);

        $productDataStorage->set(self::PRODUCT_ID_FIFTH, [
            'cpc' => [
                self::DOMAIN_ID_FIRST => 4,
                self::DOMAIN_ID_SECOND => 5,
            ],
            'cpc_search' => [
                self::DOMAIN_ID_FIRST => 2,
                self::DOMAIN_ID_SECOND => 6,
            ],
            'show' => [
                self::DOMAIN_ID_FIRST => true,
                self::DOMAIN_ID_SECOND => false,
            ],
        ]);
    }

    /**
     * @return \Shopsys\Plugin\DataStorageInterface
     */
    private function getProductDataStorage()
    {
        return $this->pluginDataStorageProvider->getDataStorage(ShopsysProductFeedZboziBundle::class, 'product');
    }
}
