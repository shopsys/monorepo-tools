<?php

namespace Shopsys\ShopBundle\Form\Front\Login;

use Shopsys\FrameworkBundle\Form\Constraints\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('email', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter e-mail']),
                    new Email(),
                ],
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter password']),
                ],
            ])
            ->add('rememberMe', CheckboxType::class, [
                'required' => false,
            ])
            ->add('login', SubmitType::class);
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
