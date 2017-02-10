<?php

namespace Shopsys\ShopBundle\Form\Front\Customer;

use Shopsys\ShopBundle\Component\Constraints\FieldsAreNotIdentical;
use Shopsys\ShopBundle\Component\Constraints\NotIdenticalToEmailLocalPart;
use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Customer\UserData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class UserFormType extends AbstractType
{

    /**
     * @return string
     */
    public function getName() {
        return 'user_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('firstName', FormType::TEXT, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter first name']),
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'First name cannot be longer then {{ limit }} characters']),
                ],
            ])
            ->add('lastName', FormType::TEXT, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter surname']),
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'Surname cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('email', FormType::EMAIL, [
                'read_only' => true,
                'required' => false,
            ])
            ->add('password', FormType::REPEATED, [
                'type' => FormType::PASSWORD,
                'required' => false,
                'options' => [
                    'attr' => ['autocomplete' => 'off'],
                ],
                'first_options' => [
                    'constraints' => [
                        new Constraints\Length(['min' => 6, 'minMessage' => 'Password cannot be longer then {{ limit }} characters']),
                    ],
                    'attr' => ['autocomplete' => 'off'],
                ],
                'invalid_message' => 'Passwords do not match',
            ]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'data_class' => UserData::class,
            'attr' => ['novalidate' => 'novalidate'],
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
