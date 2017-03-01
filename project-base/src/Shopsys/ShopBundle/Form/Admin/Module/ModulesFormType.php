<?php

namespace Shopsys\ShopBundle\Form\Admin\Module;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Module\ModuleList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModulesFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('modules', FormType::FORM)
            ->add('save', FormType::SUBMIT);

        foreach ($options['module_list']->getTranslationsIndexedByValue() as $moduleName => $moduleTranslation) {
            $builder->get('modules')
                ->add($moduleName, FormType::YES_NO, [
                    'label' => $moduleTranslation,
                ]);
        }
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('module_list')
            ->setAllowedTypes('module_list', ModuleList::class)
            ->setDefaults([
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }
}
