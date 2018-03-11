<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Vat;

use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class VatSettingsFormType extends AbstractType
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade
     */
    private $vatFacade;

    public function __construct(VatFacade $vatFacade)
    {
        $this->vatFacade = $vatFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builderSettingsGroup = $builder->create('settings', FormType::class, [
            'inherit_data' => true,
            'is_group_container' => true,
            'label' => t('Settings'),
        ]);

        $builderSettingsGroup
            ->add('defaultVat', ChoiceType::class, [
                'required' => true,
                'choices' => $this->vatFacade->getAll(),
                'choice_label' => 'name',
                'choice_value' => 'id',
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter default VAT']),
                ],
                'label' => t('Default VAT rate'),
            ])
            ->add('roundingType', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    t('To hundredths (cents)') => PricingSetting::ROUNDING_TYPE_HUNDREDTHS,
                    t('To fifty hundredths (halfs)') => PricingSetting::ROUNDING_TYPE_FIFTIES,
                    t('To whole numbers') => PricingSetting::ROUNDING_TYPE_INTEGER,
                ],
                'label' => t('Price including VAT rounding'),
            ]);

        $builder
            ->add($builderSettingsGroup)
            ->add('save', SubmitType::class);
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
