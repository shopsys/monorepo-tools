<?php

namespace Shopsys\ShopBundle\Form\Admin\Product\Flag;

use Shopsys\ShopBundle\Form\ColorPickerType;
use Shopsys\ShopBundle\Form\Locale\LocalizedType;
use Shopsys\ShopBundle\Model\Product\Flag\FlagData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class FlagFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', LocalizedType::class, [
                'required' => true,
                'options' => [
                    'constraints' => [
                        new Constraints\NotBlank(['message' => 'Please enter flag name in all languages']),
                        new Constraints\Length(['max' => 100, 'maxMessage' => 'Flag name cannot be longer than {{ limit }} characters']),
                    ],
                ],
            ])
            ->add('rgbColor', ColorPickerType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter flag color']),
                    new Constraints\Length([
                        'max' => 7,
                        'maxMessage' => 'Flag color in must be in valid hexadecimal code e.g. #3333ff',
                    ]),
                ],
            ])
            ->add('visible', CheckboxType::class, ['required' => false]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FlagData::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
