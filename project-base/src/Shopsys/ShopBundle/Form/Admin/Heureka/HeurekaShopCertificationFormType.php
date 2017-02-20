<?php

namespace Shopsys\ShopBundle\Form\Admin\Heureka;

use Shopsys\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class HeurekaShopCertificationFormType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'heureka_shop_certification_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('heurekaApiKey', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length([
                        'min' => 32,
                        'max' => 32,
                        'exactMessage' => 'Heureka API must be {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('heurekaWidgetCode', FormType::TEXTAREA, [
                'required' => false,
            ])
            ->add('save', FormType::SUBMIT);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
