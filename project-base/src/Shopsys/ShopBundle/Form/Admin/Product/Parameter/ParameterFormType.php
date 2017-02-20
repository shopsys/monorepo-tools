<?php

namespace Shopsys\ShopBundle\Form\Admin\Product\Parameter;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Product\Parameter\ParameterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ParameterFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ParameterData::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
