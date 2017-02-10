<?php

namespace Shopsys\ShopBundle\Form\Admin\QuickSearch;

use Shopsys\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QuickSearchFormType extends AbstractType
{

    /**
     * @return string
     */
    public function getName() {
        return 'quick_search_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->setMethod('GET')
            ->add('text', FormType::TEXT, [
                'required' => false,
            ])
            ->add('submit', FormType::SUBMIT);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
            'csrf_protection' => false,
            'data_class' => QuickSearchFormData::class,
        ]);
    }
}
