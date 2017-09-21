<?php

namespace Shopsys\ShopBundle\Component\Plugin;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;

class PluginCrudExtensionFacade
{
    /**
     * @var \Shopsys\ShopBundle\Component\Plugin\PluginCrudExtensionRegistry
     */
    private $pluginCrudExtensionRegistry;

    public function __construct(PluginCrudExtensionRegistry $pluginCrudExtensionRegistry)
    {
        $this->pluginCrudExtensionRegistry = $pluginCrudExtensionRegistry;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param string $type
     * @param string $name
     */
    public function extendForm(FormBuilderInterface $builder, $type, $name)
    {
        $builder->add($name, FormType::class, [
            'compound' => true,
        ]);

        foreach ($this->pluginCrudExtensionRegistry->getCrudExtensions($type) as $key => $crudExtension) {
            $builder->get($name)->add($key, $crudExtension->getFormTypeClass(), [
                'label' => $crudExtension->getFormLabel(),
            ]);
        }
    }

    /**
     * @param string $type
     * @param int $id
     * @return array
     */
    public function getAllData($type, $id)
    {
        $allData = [];
        foreach ($this->pluginCrudExtensionRegistry->getCrudExtensions($type) as $key => $crudExtension) {
            $allData[$key] = $crudExtension->getData($id);
        }

        return $allData;
    }

    /**
     * @param string $type
     * @param int $id
     * @param array $allData
     */
    public function saveAllData($type, $id, array $allData)
    {
        foreach ($this->pluginCrudExtensionRegistry->getCrudExtensions($type) as $key => $crudExtension) {
            if (array_key_exists($key, $allData)) {
                $crudExtension->saveData($id, $allData[$key]);
            }
        }
    }

    /**
     * @param string $type
     * @param int $id
     */
    public function removeAllData($type, $id)
    {
        foreach ($this->pluginCrudExtensionRegistry->getCrudExtensions($type) as $crudExtension) {
            $crudExtension->removeData($id);
        }
    }
}
