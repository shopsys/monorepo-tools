<?php

namespace Shopsys\ProductFeed\HeurekaBundle;

use Shopsys\Plugin\PluginDataStorageProviderInterface;

class DataStorageProvider
{
    const CONTEXT_PRODUCT = 'product';

    /**
     * @var \Shopsys\Plugin\PluginDataStorageProviderInterface
     */
    private $pluginDataStorageProvider;

    /**
     * @param \Shopsys\Plugin\PluginDataStorageProviderInterface $dataStorageProvider
     */
    public function __construct(PluginDataStorageProviderInterface $dataStorageProvider)
    {
        $this->pluginDataStorageProvider = $dataStorageProvider;
    }

    /**
     * @return \Shopsys\Plugin\DataStorageInterface
     */
    public function getProductDataStorage()
    {
        return $this->pluginDataStorageProvider
            ->getDataStorage(ShopsysProductFeedHeurekaBundle::class, self::CONTEXT_PRODUCT);
    }
}
