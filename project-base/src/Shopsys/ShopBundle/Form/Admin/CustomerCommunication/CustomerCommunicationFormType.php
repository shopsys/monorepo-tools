<?php

namespace Shopsys\ShopBundle\Form\Admin\CustomerCommunication;

use Shopsys\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CustomerCommunicationFormType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'customer_communication_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', FormType::WYSIWYG, ['required' => false])
            ->add('save', FormType::SUBMIT);
    }
}
