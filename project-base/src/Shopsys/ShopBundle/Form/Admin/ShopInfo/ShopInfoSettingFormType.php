<?php

namespace Shopsys\ShopBundle\Form\Admin\ShopInfo;

use Shopsys\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ShopInfoSettingFormType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'shop_info_setting_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phoneNumber', FormType::TEXT, [
                'required' => false,
            ])
            ->add('email', FormType::TEXT, [
                'required' => false,
            ])
            ->add('phoneHours', FormType::TEXT, [
                'required' => false,
            ])
            ->add('save', FormType::SUBMIT);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
