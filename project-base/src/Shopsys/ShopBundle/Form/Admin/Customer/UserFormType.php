<?php

namespace Shopsys\ShopBundle\Form\Admin\Customer;

use Shopsys\ShopBundle\Component\Constraints\Email;
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
     * @var string
     */
    private $scenario;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\SelectedDomain
     */
    private $selectedDomain;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup[]
     */
    private $pricingGroups;

    /**
     * @param string $scenario
     * @param \Shopsys\ShopBundle\Component\Domain\SelectedDomain $selectedDomain
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup[]|null $pricingGroups
     */
    public function __construct($scenario, $selectedDomain = null, $pricingGroups = null)
    {
        $this->scenario = $scenario;
        $this->selectedDomain = $selectedDomain;
        $this->pricingGroups = $pricingGroups;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter e-mail']),
                    new Constraints\Length(['max' => 255, 'maxMessage' => 'Email cannot be longer then {{ limit }} characters']),
                    new Email(['message' => 'Please enter valid e-mail']),
                ],
            ])
            ->add('password', FormType::REPEATED, [
                'type' => FormType::PASSWORD,
                'required' => $this->scenario === CustomerFormType::SCENARIO_CREATE,
                'options' => [
                    'attr' => ['autocomplete' => 'off'],
                ],
                'first_options' => [
                    'constraints' => [
                        new Constraints\NotBlank([
                            'message' => 'Please enter password',
                            'groups' => [CustomerFormType::SCENARIO_CREATE],
                        ]),
                        new Constraints\Length(['min' => 6, 'minMessage' => 'Password cannot be longer then {{ limit }} characters']),
                    ],
                ],
                'invalid_message' => 'Passwords do not match',
            ]);

        if ($this->scenario === CustomerFormType::SCENARIO_CREATE) {
            $builder
                ->add('domainId', FormType::DOMAIN, [
                    'required' => true,
                    'data' => $this->selectedDomain->getId(),
                ]);
        }

        $builder
            ->add('pricingGroup', FormType::CHOICE, [
                'required' => true,
                'choices' => $this->pricingGroups,
                'choices_as_values' => true,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'group_by' => $this->scenario === CustomerFormType::SCENARIO_CREATE ? 'domainId' : null,
            ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
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
