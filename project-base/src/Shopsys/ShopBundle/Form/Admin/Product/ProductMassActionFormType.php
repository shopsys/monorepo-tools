<?php

namespace Shopsys\ShopBundle\Form\Admin\Product;

use Shopsys\ShopBundle\Model\Product\MassAction\ProductMassActionData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductMassActionFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('selectType', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    t('Only checked products') => ProductMassActionData::SELECT_TYPE_CHECKED,
                    t('All search results') => ProductMassActionData::SELECT_TYPE_ALL_RESULTS,
                ],
                'choices_as_values' => true, // Switches to Symfony 3 choice mode, remove after upgrade from 2.8
            ])
            ->add('action', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    t('Set') => ProductMassActionData::ACTION_SET,
                ],
                'choices_as_values' => true, // Switches to Symfony 3 choice mode, remove after upgrade from 2.8
            ])
            ->add('subject', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    t('Hiding product') => ProductMassActionData::SUBJECT_PRODUCT_HIDDEN,
                ],
                'choices_as_values' => true, // Switches to Symfony 3 choice mode, remove after upgrade from 2.8
            ])
            ->add('value', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    t('Hide') => ProductMassActionData::VALUE_PRODUCT_HIDE,
                    t('Display') => ProductMassActionData::VALUE_PRODUCT_SHOW,
                ],
                'choices_as_values' => true, // Switches to Symfony 3 choice mode, remove after upgrade from 2.8
            ])
            ->add('submit', SubmitType::class);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
            'data_class' => ProductMassActionData::class,
        ]);
    }
}
