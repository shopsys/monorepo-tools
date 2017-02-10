<?php

namespace Shopsys\ShopBundle\Form\Admin\Product;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Product\MassAction\ProductMassActionData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductMassActionFormType extends AbstractType
{

    /**
     * @return string
     */
    public function getName() {
        return 'mass_action_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('selectType', FormType::CHOICE, [
                'required' => true,
                'choices' => [
                    ProductMassActionData::SELECT_TYPE_CHECKED => t('Only checked products'),
                    ProductMassActionData::SELECT_TYPE_ALL_RESULTS => t('All search results'),
                ],
            ])
            ->add('action', FormType::CHOICE, [
                'required' => true,
                'choices' => [
                    ProductMassActionData::ACTION_SET => t('Set'),
                ],
            ])
            ->add('subject', FormType::CHOICE, [
                'required' => true,
                'choices' => [
                    ProductMassActionData::SUBJECT_PRODUCT_HIDDEN => t('Hiding product'),
                ],
            ])
            ->add('value', FormType::CHOICE, [
                'required' => true,
                'choices' => [
                    ProductMassActionData::VALUE_PRODUCT_HIDE => t('Hide'),
                    ProductMassActionData::VALUE_PRODUCT_SHOW => t('Display'),
                ],
            ])
            ->add('submit', FormType::SUBMIT);
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
            'data_class' => ProductMassActionData::class,
        ]);
    }

}
