<?php

namespace Shopsys\ShopBundle\Form\Admin\Product\Unit;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Product\Unit\UnitData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class UnitFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', FormType::LOCALIZED, [
                'required' => true,
                'options' => [
                    'constraints' => [
                        new Constraints\NotBlank(['message' => 'Please enter unit name in all languages']),
                        new Constraints\Length(['max' => 10, 'maxMessage' => 'Unit name cannot be longer than {{ limit }} characters']),
                    ],
                ],
            ]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UnitData::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
