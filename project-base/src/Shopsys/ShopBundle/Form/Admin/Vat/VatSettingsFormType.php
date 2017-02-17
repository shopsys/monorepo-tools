<?php

namespace Shopsys\ShopBundle\Form\Admin\Vat;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;
use Shopsys\ShopBundle\Model\Pricing\Rounding;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class VatSettingsFormType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Vat\VatFacade
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
        $roundingTypeChoices = [
            PricingSetting::ROUNDING_TYPE_HUNDREDTHS => t('To hundredths (cents)'),
            PricingSetting::ROUNDING_TYPE_FIFTIES => t('To fifty hundredths (halfs)'),
            PricingSetting::ROUNDING_TYPE_INTEGER => t('To whole numbers'),
        ];

        if (array_keys($roundingTypeChoices) !== PricingSetting::getRoundingTypes()) {
            throw new \Shopsys\ShopBundle\Form\Exception\InconsistentChoicesException(
                'Rounding type choices in ' . __CLASS__ . ' are not consistent with PricingSetting::getRoundingTypes().'
            );
        }

        $builder
            ->add('defaultVat', FormType::CHOICE, [
                'required' => true,
                'choice_list' => new ObjectChoiceList($this->vatFacade->getAll(), 'name', [], null, 'id'),
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter default VAT']),
                ],
            ])
            ->add('roundingType', FormType::CHOICE, [
                'required' => true,
                'choices' => $roundingTypeChoices,
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
