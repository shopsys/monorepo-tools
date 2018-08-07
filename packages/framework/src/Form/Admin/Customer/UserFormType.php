<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Customer;

use Shopsys\FrameworkBundle\Form\Constraints\Email;
use Shopsys\FrameworkBundle\Form\Constraints\FieldsAreNotIdentical;
use Shopsys\FrameworkBundle\Form\Constraints\NotIdenticalToEmailLocalPart;
use Shopsys\FrameworkBundle\Form\Constraints\UniqueEmail;
use Shopsys\FrameworkBundle\Form\DisplayOnlyDomainIconType;
use Shopsys\FrameworkBundle\Form\DisplayOnlyType;
use Shopsys\FrameworkBundle\Form\DomainType;
use Shopsys\FrameworkBundle\Form\GroupType;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Customer\UserData;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade;
use Shopsys\FrameworkBundle\Twig\DateTimeFormatterExtension;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class UserFormType extends AbstractType
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade
     */
    private $pricingGroupFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Twig\DateTimeFormatterExtension
     */
    private $dateTimeFormatterExtension;

    public function __construct(
        PricingGroupFacade $pricingGroupFacade,
        DateTimeFormatterExtension $dateTimeFormatterExtension
    ) {
        $this->pricingGroupFacade = $pricingGroupFacade;
        $this->dateTimeFormatterExtension = $dateTimeFormatterExtension;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        /* @var $user \Shopsys\FrameworkBundle\Model\Customer\User */

        $builderSystemDataGroup = $builder->create('systemData', GroupType::class, [
            'label' => t('System data'),
        ]);

        if ($user instanceof User) {
            $builderSystemDataGroup->add('formId', DisplayOnlyType::class, [
                'label' => t('ID'),
                'data' => $user->getId(),
            ]);
            $builderSystemDataGroup->add('domainIcon', DisplayOnlyDomainIconType::class, [
                'data' => $user->getDomainId(),
            ]);
            $pricingGroups = $this->pricingGroupFacade->getByDomainId($options['domain_id']);
            $groupPricingGroupsBy = null;
        } else {
            $builderSystemDataGroup
                ->add('domainId', DomainType::class, [
                    'required' => true,
                    'data' => $options['domain_id'],
                    'label' => t('Domain'),
                    'attr' => [
                        'class' => 'js-toggle-opt-group-control',
                    ],
                ]);
            $pricingGroups = $this->pricingGroupFacade->getAll();
            $groupPricingGroupsBy = 'domainId';
        }

        $builderSystemDataGroup
            ->add('pricingGroup', ChoiceType::class, [
                'required' => true,
                'choices' => $pricingGroups,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'group_by' => $groupPricingGroupsBy,
                'label' => t('Pricing group'),
                'attr' => [
                    'class' => 'js-toggle-opt-group',
                    'data-js-toggle-opt-group-control' => '.js-toggle-opt-group-control',
                ],
            ]);

        $builderPersonalDataGroup = $builder->create('personalData', GroupType::class, [
            'label' => t('Personal data'),
        ]);

        $builderPersonalDataGroup
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter first name']),
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'First name cannot be longer then {{ limit }} characters',
                    ]),
                ],
                'label' => t('First name'),
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter last name']),
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'Last name cannot be longer than {{ limit }} characters',
                    ]),
                ],
                'label' => t('Last name'),
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter e-mail']),
                    new Constraints\Length([
                        'max' => 255,
                        'maxMessage' => 'Email cannot be longer then {{ limit }} characters',
                    ]),
                    new Email(['message' => 'Please enter valid e-mail']),
                    new UniqueEmail(['ignoredEmail' => $user !== null ? $user->getEmail() : null]),
                ],
                'label' => t('E-mail'),
            ]);

        $builderRegisteredCustomerGroup = $builder->create('registeredCustomer', GroupType::class, [
            'label' => t('Registered cust.'),
        ]);

        $builderRegisteredCustomerGroup
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => $options['user'] === null,
                'options' => [
                    'attr' => ['autocomplete' => 'off'],
                ],
                'first_options' => [
                    'constraints' => $this->getFirstPasswordConstraints($options['user'] === null),
                    'label' => t('Password'),
                ],
                'second_options' => [
                    'label' => t('Password again'),
                ],
                'invalid_message' => 'Passwords do not match',
            ]);

        if ($user instanceof User) {
            $builderSystemDataGroup->add('createdAt', DisplayOnlyType::class, [
                'label' => t('Date of registration and privacy policy agreement'),
                'data' => $this->dateTimeFormatterExtension->formatDateTime($user->getCreatedAt()),
            ]);

            $builderRegisteredCustomerGroup->add('lastLogin', DisplayOnlyType::class, [
                'label' => t('Last login'),
                'data' => $user->getLastLogin() !== null ? $this->dateTimeFormatterExtension->formatDateTime($user->getLastLogin()) : t('never'),
            ]);
        }

        $builder
            ->add($builderSystemDataGroup)
            ->add($builderPersonalDataGroup)
            ->add($builderRegisteredCustomerGroup);
    }

    /**
     * @param bool $isCreatingNewUser
     * @return \Symfony\Component\Validator\Constraint[]
     */
    private function getFirstPasswordConstraints($isCreatingNewUser)
    {
        $constraints = [
            new Constraints\Length(['min' => 6, 'minMessage' => 'Password cannot be longer then {{ limit }} characters']),
        ];

        if ($isCreatingNewUser) {
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
            ->setRequired(['user', 'domain_id'])
            ->setAllowedTypes('user', [User::class, 'null'])
            ->setAllowedTypes('domain_id', 'int')
            ->setDefaults([
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
