<?php

namespace Shopsys\ProductFeed\ZboziBundle;

use Shopsys\Plugin\PluginCrudExtensionInterface;
use Shopsys\Plugin\PluginDataStorageProviderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ZboziProductCrudExtension implements PluginCrudExtensionInterface
{
    /**
     * @var \Shopsys\Plugin\PluginDataStorageProviderInterface
     */
    private $pluginDataStorageProvider;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    public function __construct(
        PluginDataStorageProviderInterface $pluginDataStorageProvider,
        TranslatorInterface $translator
    ) {
        $this->pluginDataStorageProvider = $pluginDataStorageProvider;
        $this->translator = $translator;
    }

    /**
     * @return string
     */
    public function getFormTypeClass()
    {
        return ZboziProductFormType::class;
    }

    /**
     * @return string
     */
    public function getFormLabel()
    {
        return $this->translator->trans('Zbozi.cz product feed');
    }

    /**
     * @param int $productId
     * @return array
     */
    public function getData($productId)
    {
        return $this->getProductDataStorage()->get($productId) ?? [];
    }

    /**
     * @param int $productId
     * @param array $data
     */
    public function saveData($productId, $data)
    {
        $this->getProductDataStorage()->set($productId, $data);
    }

    /**
     * @param int $productId
     */
    public function removeData($productId)
    {
        $this->getProductDataStorage()->remove($productId);
    }

    /**
     * @return \Shopsys\Plugin\DataStorageInterface
     */
    private function getProductDataStorage()
    {
        return $this->pluginDataStorageProvider->getDataStorage(ShopsysProductFeedZboziBundle::class, 'product');
    }
}
