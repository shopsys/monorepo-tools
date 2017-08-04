<?php

namespace Shopsys\ShopBundle\Form\Admin\Customer;

use Shopsys\ShopBundle\Component\Constraints\Email;
use Shopsys\ShopBundle\Component\Constraints\FieldsAreNotIdentical;
use Shopsys\ShopBundle\Component\Constraints\NotIdenticalToEmailLocalPart;
use Shopsys\ShopBundle\Form\DomainType;
use Shopsys\ShopBundle\Model\Customer\UserData;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
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
     * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupFacade
     */
    private $pricingGroupFacade;

    public function __construct(PricingGroupFacade $pricingGroupFacade)
    {
        $this->pricingGroupFacade = $pricingGroupFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter first name']),
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'First name cannot be longer then {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter last name']),
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'Last name cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter e-mail']),
                    new Constraints\Length([
                        'max' => 255,
                        'maxMessage' => 'Email cannot be longer then {{ limit }} characters',
                    ]),
                    new Email(['message' => 'Please enter valid e-mail']),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => $options['scenario'] === CustomerFormType::SCENARIO_CREATE,
                'options' => [
                    'attr' => ['autocomplete' => 'off'],
                ],
                'first_options' => [
                    'constraints' => $this->getFirstPasswordConstraints($options['scenario']),
                ],
                'invalid_message' => 'Passwords do not match',
            ]);

        if ($options['scenario'] === CustomerFormType::SCENARIO_CREATE) {
            $builder
                ->add('domainId', DomainType::class, [
                    'required' => true,
                    'data' => $options['domain_id'],
                ]);
            $pricingGroups = $this->pricingGroupFacade->getAll();
            $groupPricingGroupsBy = 'domainId';
        } else {
            $pricingGroups = $this->pricingGroupFacade->getByDomainId($options['domain_id']);
            $groupPricingGroupsBy = null;
        }

        $builder
            ->add('pricingGroup', ChoiceType::class, [
                'required' => true,
                'choices' => $pricingGroups,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'group_by' => $groupPricingGroupsBy,
            ]);
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

        if ($scenario === CustomerFormType::SCENARIO_CREATE) {
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
            ->setRequired(['scenario', 'domain_id'])
            ->setAllowedValues('scenario', [CustomerFormType::SCENARIO_CREATE, CustomerFormType::SCENARIO_EDIT])
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
