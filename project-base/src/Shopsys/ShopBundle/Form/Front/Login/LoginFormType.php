<?php

namespace Shopsys\ShopBundle\Form\Front\Login;

use Shopsys\ShopBundle\Component\Constraints\Email;
use Shopsys\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class LoginFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', FormType::TEXT, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter e-mail']),
                    new Email(),
                ],
            ])
            ->add('password', FormType::PASSWORD, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter password']),
                ],
            ])
            ->add('rememberMe', FormType::CHECKBOX, [
                'required' => false,
            ])
            ->add('login', FormType::SUBMIT);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'front_login_form';
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
