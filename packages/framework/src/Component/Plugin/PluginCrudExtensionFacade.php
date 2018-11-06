<?php

namespace Shopsys\FrameworkBundle\Component\Plugin;

use Shopsys\FrameworkBundle\Form\GroupType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;

class PluginCrudExtensionFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionRegistry
     */
    protected $pluginCrudExtensionRegistry;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionRegistry $pluginCrudExtensionRegistry
     */
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
            'render_form_row' => false,
        ]);

        $crudExtensions = $this->pluginCrudExtensionRegistry->getCrudExtensions($type);
        foreach ($crudExtensions as $key => $crudExtension) {
            $builderExtensionGroup = $builder->create($key . 'Group', GroupType::class, [
                'label' => $crudExtension->getFormLabel(),
                'is_group_container_to_render_as_the_last_one' => end($crudExtensions) === $crudExtension,
            ]);

            $builderExtensionGroup->add($key, $crudExtension->getFormTypeClass(), [
                'render_form_row' => false,
            ]);

            $builder->get($name)->add($builderExtensionGroup);
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
