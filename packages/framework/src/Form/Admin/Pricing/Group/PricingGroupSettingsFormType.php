<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Pricing\Group;

use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class PricingGroupSettingsFormType extends AbstractType
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade
     */
    private $pricingGroupFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade $pricingGroupFacade
     */
    public function __construct(PricingGroupFacade $pricingGroupFacade)
    {
        $this->pricingGroupFacade = $pricingGroupFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $pricingGroups = $this->pricingGroupFacade->getByDomainId($options['domain_id']);

        $builder
            ->add('defaultPricingGroup', ChoiceType::class, [
                'required' => true,
                'choices' => $pricingGroups,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter default pricing group']),
                ],
            ])
            ->add('save', SubmitType::class);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('domain_id')
            ->setAllowedTypes('domain_id', 'int')
            ->setDefaults([
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }
}
