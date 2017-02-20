<?php

namespace Shopsys\ShopBundle\Form\Admin\Country;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Country\CountryData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class CountryFormType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'country_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', FormType::TEXT, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter country name']),
                    new Constraints\Length(['max' => 255, 'maxMessage' => 'Country name cannot be longer than {{ limit }} characters']),
                ],
            ]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CountryData::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
