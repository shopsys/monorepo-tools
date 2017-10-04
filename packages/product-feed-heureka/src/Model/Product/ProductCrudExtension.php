<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\Product;

use Shopsys\Plugin\PluginCrudExtensionInterface;
use Shopsys\ProductFeed\HeurekaBundle\DataStorageProvider;
use Symfony\Component\Translation\TranslatorInterface;

class ProductCrudExtension implements PluginCrudExtensionInterface
{

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\DataStorageProvider
     */
    private $dataStorageProvider;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @param \Shopsys\ProductFeed\HeurekaBundle\DataStorageProvider $dataStorageProvider
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     */
    public function __construct(
        DataStorageProvider $dataStorageProvider,
        TranslatorInterface $translator
    ) {
        $this->dataStorageProvider = $dataStorageProvider;
        $this->translator = $translator;
    }

    /**
     * @return string
     */
    public function getFormTypeClass()
    {
        return ProductFormType::class;
    }

    /**
     * @return string
     */
    public function getFormLabel()
    {
        return $this->translator->trans('Heureka.cz product feed');
    }

    /**
     * @param int $productId
     * @return array
     */
    public function getData($productId)
    {
        $data = $this->dataStorageProvider->getProductDataStorage()
            ->get($productId);

        return $data ?? [];
    }

    /**
     * @param int $productId
     * @param array $data
     */
    public function saveData($productId, $data)
    {
        $this->dataStorageProvider->getProductDataStorage()
            ->set($productId, $data);
    }

    /**
     * @param int $productId
     */
    public function removeData($productId)
    {
        $this->dataStorageProvider->getProductDataStorage()
            ->remove($productId);
    }
}
