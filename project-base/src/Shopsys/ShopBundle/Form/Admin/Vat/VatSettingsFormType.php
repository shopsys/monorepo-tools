<?php

namespace Shopsys\ShopBundle\Form\Admin\Vat;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class VatSettingsFormType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Vat\Vat[]
     */
    private $vats;

    /**
     * @var array
     */
    private $roundingTypes;

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Vat\Vat[] $vats
     * @param array $roundingTypes
     */
    public function __construct(
        array $vats,
        array $roundingTypes
    ) {
        $this->vats = $vats;
        $this->roundingTypes = $roundingTypes;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $roundingTypesLabels = $this->getRoundingTypesLabels();

        $roundingTypesChoices = [];
        foreach ($this->roundingTypes as $roundingType) {
            $roundingTypesChoices[$roundingType] = $roundingTypesLabels[$roundingType];
        }

        $builder
            ->add('defaultVat', FormType::CHOICE, [
                'required' => true,
                'choice_list' => new ObjectChoiceList($this->vats, 'name', [], null, 'id'),
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter default VAT']),
                ],
            ])
            ->add('roundingType', FormType::CHOICE, [
                'required' => true,
                'choices' => $roundingTypesChoices,
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

    /**
     * @return array
     */
    private function getRoundingTypesLabels()
    {
        return [
            PricingSetting::ROUNDING_TYPE_HUNDREDTHS => t('To hundredths (cents)'),
            PricingSetting::ROUNDING_TYPE_FIFTIES => t('To fifty hundredths (halfs)'),
            PricingSetting::ROUNDING_TYPE_INTEGER => t('To whole numbers'),
        ];
    }
}
