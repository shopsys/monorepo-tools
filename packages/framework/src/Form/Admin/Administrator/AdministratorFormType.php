<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Administrator;

use Shopsys\FrameworkBundle\Form\Constraints\Email;
use Shopsys\FrameworkBundle\Form\Constraints\FieldsAreNotIdentical;
use Shopsys\FrameworkBundle\Form\DisplayOnlyType;
use Shopsys\FrameworkBundle\Form\GroupType;
use Shopsys\FrameworkBundle\Model\Administrator\Administrator;
use Shopsys\FrameworkBundle\Model\Administrator\AdministratorData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class AdministratorFormType extends AbstractType
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_EDIT = 'edit';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builderSettingsGroup = $builder->create('settings', GroupType::class, [
            'label' => t('Settings'),
        ]);

        if ($options['scenario'] === self::SCENARIO_EDIT) {
            $builderSettingsGroup
                ->add('id', DisplayOnlyType::class, [
                    'constraints' => [
                        new Constraints\NotBlank(['message' => 'Please enter article name']),
                    ],
                    'data' => $options['administrator']->getId(),
                    'label' => t('ID'),
                ]);
        }

        $builderSettingsGroup
            ->add('username', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter username']),
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'Username cannot be longer then {{ limit }} characters']),
                ],
                'label' => t('Login name'),
            ])
            ->add('realName', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter full name']),
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'Full name cannot be longer then {{ limit }} characters']),
                ],
                'label' => t('Full name'),
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'constraints' => [
                    new Email(['message' => 'Please enter valid e-mail']),
                    new Constraints\NotBlank(['message' => 'Please enter e-mail']),
                    new Constraints\Length(['max' => 255, 'maxMessage' => 'Email cannot be longer then {{ limit }} characters']),
                ],
                'label' => t('E-mail'),
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => $options['scenario'] === self::SCENARIO_CREATE,
                'options' => [
                    'attr' => ['autocomplete' => 'off'],
                ],
                'first_options' => [
                    'label' => t('Password'),
                    'constraints' => $this->getFirstPasswordConstraints($options['scenario']),
                    'icon_title' => t('Password must be at least six characters and can\'t be the same as login name.'),

                ],
                'second_options' => [
                    'label' => t('Password again'),
                ],
                'invalid_message' => 'Passwords do not match',
                'label' => t('Password'),
            ]);

        $builder
            ->add($builderSettingsGroup)
            ->add('save', SubmitType::class);
    }

    /**
     * @param string $scenario
     * @return \Symfony\Component\Validator\Constraint[]
     */
    private function getFirstPasswordConstraints($scenario)
    {
        $constraints = [
            new Constraints\Length(['min' => 6, 'minMessage' => 'Password cannot be longer then {{ limit }} characters']),
        ];

        if ($scenario === self::SCENARIO_CREATE) {
            $constraints[] = new Constraints\NotBlank([
                'message' => 'Please enter password',
            ]);
        }

        return $constraints;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['administrator', 'scenario'])
            ->setAllowedTypes('administrator', [Administrator::class, 'null'])
            ->setAllowedValues('scenario', [self::SCENARIO_CREATE, self::SCENARIO_EDIT])
            ->setDefaults([
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
