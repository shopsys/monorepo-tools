<?php

namespace Shopsys\Plugin;

/**
 * @deprecated
 */
interface PluginDataStorageProviderInterface
{
    const CONTEXT_DEFAULT = 'default';

    /**
     * Provides a plugin custom data repository for a given context
     *
     * @param string $pluginName FQCN of the plugin bundle class
     * @param string $context name of the custom data context (eg. "product" for saving additional data to products)
     * @return \Shopsys\Plugin\DataStorageInterface
     */
    public function getDataStorage($pluginName, $context = self::CONTEXT_DEFAULT);
}
