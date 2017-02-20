<?php

namespace Shopsys\ShopBundle\Form\Admin\Administrator;

use Shopsys\ShopBundle\Component\Constraints\Email;
use Shopsys\ShopBundle\Component\Constraints\FieldsAreNotIdentical;
use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Administrator\AdministratorData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class AdministratorFormType extends AbstractType
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_EDIT = 'edit';

    private $scenario;

    public function __construct($scenario)
    {
        $this->scenario = $scenario;
    }

    public function getName()
    {
        return 'administrator_form';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', FormType::TEXT, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter username']),
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'Username cannot be longer then {{ limit }} characters']),
                ],
            ])
            ->add('realName', FormType::TEXT, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter full name']),
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'Full name cannot be longer then {{ limit }} characters']),
                ],
            ])
            ->add('email', FormType::EMAIL, [
                'required' => true,
                'constraints' => [
                    new Email(['message' => 'Please enter valid e-mail']),
                    new Constraints\NotBlank(['message' => 'Please enter e-mail']),
                    new Constraints\Length(['max' => 255, 'maxMessage' => 'Email cannot be longer then {{ limit }} characters']),
                ],
            ])
            ->add('password', FormType::REPEATED, [
                'type' => FormType::PASSWORD,
                'required' => $this->scenario === self::SCENARIO_CREATE,
                'options' => [
                    'attr' => ['autocomplete' => 'off'],
                ],
                'first_options' => [
                    'constraints' => [
                        new Constraints\NotBlank([
                            'message' => 'Please enter password',
                            'groups' => [self::SCENARIO_CREATE],
                        ]),
                        new Constraints\Length(['min' => 6, 'minMessage' => 'Password cannot be longer then {{ limit }} characters']),
                    ],
                ],
                'invalid_message' => 'Passwords do not match',
            ])
            ->add('save', FormType::SUBMIT);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AdministratorData::class,
            'attr' => ['novalidate' => 'novalidate'],
            'constraints' => [
                new FieldsAreNotIdentical([
                    'field1' => 'username',
                    'field2' => 'password',
                    'errorPath' => 'password',
                    'message' => 'Password cannot be same as username',
                ]),
            ],
        ]);
    }
}
