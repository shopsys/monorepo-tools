<?php

namespace Shopsys\ShopBundle\Form\Front\Registration;

use Shopsys\FrameworkBundle\Form\Constraints\Email;
use Shopsys\FrameworkBundle\Form\Constraints\FieldsAreNotIdentical;
use Shopsys\FrameworkBundle\Form\Constraints\NotIdenticalToEmailLocalPart;
use Shopsys\FrameworkBundle\Form\Constraints\UniqueEmail;
use Shopsys\FrameworkBundle\Form\HoneyPotType;
use Shopsys\FrameworkBundle\Form\TimedFormTypeExtension;
use Shopsys\FrameworkBundle\Model\Customer\UserData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class RegistrationFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter first name']),
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'First name cannot be longer then {{ limit }} characters']),
                ],
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter last name']),
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'Last name cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter e-mail']),
                    new Email(['message' => 'Please enter valid e-mail']),
                    new Constraints\Length(['max' => 255, 'maxMessage' => 'Email cannot be longer then {{ limit }} characters']),
                    new UniqueEmail(),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => ['autocomplete' => 'off'],
                ],
                'first_options' => [
                    'constraints' => [
                        new Constraints\NotBlank(['message' => 'Please enter password']),
                        new Constraints\Length(['min' => 6, 'minMessage' => 'Password cannot be longer then {{ limit }} characters']),
                    ],
                ],
                'invalid_message' => 'Passwords do not match',
            ])
            ->add('privacyPolicy', CheckboxType::class, [
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'You have to agree with privacy policy']),
                ],
            ])
            ->add('email2', HoneyPotType::class)
            ->add('save', SubmitType::class);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserData::class,
            'attr' => ['novalidate' => 'novalidate'],
            TimedFormTypeExtension::OPTION_ENABLED => true,
            'constraints' => [
                new FieldsAreNotIdentical([
                    'field1' => 'email',
                    'field2' => 'password',
                    'errorPath' => 'password',
                    'message' => 'Password cannot be same as e-mail',
                ]),
                new NotIdenticalToEmailLocalPart([
                    'password' => 'password',
                    'email' => 'email',
                    'errorPath' => 'password',
                    'message' => 'Password cannot be same as part of e-mail before at sign',
                ]),
            ],
        ]);
    }
}
