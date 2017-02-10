<?php

namespace Shopsys\ShopBundle\Form\Admin\Product\Parameter;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Product\Parameter\ParameterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class ParameterFormType extends AbstractType
{

    /**
     * @return string
     */
    public function getName() {
        return 'parameter_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', FormType::LOCALIZED, [
                'required' => false,
                'options' => [
                    'constraints' => [
                        new Constraints\NotBlank(['message' => 'Please enter parameter name']),
                        new Constraints\Length(['max' => 100, 'maxMessage' => 'Parameter name cannot be longer than {{ limit }} characters']),
                    ],
                ],
            ])
            ->add('visible', FormType::CHECKBOX, ['required' => false]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'data_class' => ParameterData::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }

}
