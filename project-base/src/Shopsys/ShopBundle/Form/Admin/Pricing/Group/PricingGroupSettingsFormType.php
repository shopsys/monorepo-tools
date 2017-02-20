<?php

namespace Shopsys\ShopBundle\Form\Admin\Pricing\Group;

use Shopsys\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class PricingGroupSettingsFormType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup[]
     */
    private $pricingGroups;

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup[] $pricingGroups
     */
    public function __construct(array $pricingGroups)
    {
        $this->pricingGroups = $pricingGroups;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('defaultPricingGroup', FormType::CHOICE, [
                'required' => true,
                'choice_list' => new ObjectChoiceList($this->pricingGroups, 'name', [], null, 'id'),
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter default pricing group']),
                ],
            ])
            ->add('save', FormType::SUBMIT);
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
