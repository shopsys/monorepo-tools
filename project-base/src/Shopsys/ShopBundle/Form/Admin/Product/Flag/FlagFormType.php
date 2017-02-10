<?php

namespace Shopsys\ShopBundle\Form\Admin\Product\Flag;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Product\Flag\FlagData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class FlagFormType extends AbstractType {

    /**
     * @return string
     */
    public function getName() {
        return 'flag_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', FormType::LOCALIZED, [
                'required' => true,
                'options' => [
                    'constraints' => [
                        new Constraints\NotBlank(['message' => 'Please enter flag name in all languages']),
                        new Constraints\Length(['max' => 100, 'maxMessage' => 'Flag name cannot be longer than {{ limit }} characters']),
                    ],
                ],
            ])
            ->add('rgbColor', FormType::COLOR_PICKER, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter flag color']),
                    new Constraints\Length([
                        'max' => 7,
                        'maxMessage' => 'Flag color in must be in valid hexadecimal code e.g. #3333ff',
                    ]),
                ],
            ])
            ->add('visible', FormType::CHECKBOX, ['required' => false]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'data_class' => FlagData::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }

}
