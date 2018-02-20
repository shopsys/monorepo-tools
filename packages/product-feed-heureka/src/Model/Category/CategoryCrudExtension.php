<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\Category;

use Shopsys\Plugin\PluginCrudExtensionInterface;
use Shopsys\ProductFeed\HeurekaBundle\DataStorageProvider;
use Symfony\Component\Translation\TranslatorInterface;

class CategoryCrudExtension implements PluginCrudExtensionInterface
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
        $this->translator = $translator;
        $this->dataStorageProvider = $dataStorageProvider;
    }

    /**
     * @return string
     */
    public function getFormTypeClass()
    {
        return CategoryFormType::class;
    }

    /**
     * @return string
     */
    public function getFormLabel()
    {
        return $this->translator->trans('Heureka.cz product feed');
    }

    /**
     * @param int $categoryId
     * @return array
     */
    public function getData($categoryId)
    {
        return $this->getCategoryDataStorage()->get($categoryId) ?? [];
    }

    /**
     * @param int $categoryId
     * @param array $data
     */
    public function saveData($categoryId, $data)
    {
        $this->getCategoryDataStorage()->set($categoryId, $data);
    }

    /**
     * @param int $categoryId
     */
    public function removeData($categoryId)
    {
        $this->getCategoryDataStorage()->remove($categoryId);
    }

    /**
     * @return \Shopsys\Plugin\DataStorageInterface
     */
    private function getCategoryDataStorage()
    {
        return $this->dataStorageProvider->getCategoryDataStorage();
    }
}
