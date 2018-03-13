<?php

namespace Shopsys\FrameworkBundle\Form\Admin\CustomerCommunication;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class CustomerCommunicationFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builderSettingsGroup = $builder->create('settings', FormType::class, [
            'inherit_data' => true,
            'label' => t('Settings'),
            'is_group_container' => true,
            'is_group_container_to_render_as_the_last_one' => true,
        ]);

        $builderSettingsGroup
            ->add('content', CKEditorType::class, ['required' => false]);

        $builder
            ->add($builderSettingsGroup)
            ->add('save', SubmitType::class);
    }
}
