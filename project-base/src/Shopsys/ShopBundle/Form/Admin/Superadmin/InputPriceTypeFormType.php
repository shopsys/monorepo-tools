<?php

namespace Shopsys\ShopBundle\Form\Admin\Superadmin;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class InputPriceTypeFormType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'input_price_type_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $inputPriceTypesLabels = $this->getInputPriceTypesLabels();

        $choices = [];
        foreach (PricingSetting::getInputPriceTypes() as $inputPriceType) {
            $choices[$inputPriceType] = $inputPriceTypesLabels[$inputPriceType];
        }

        $builder
            ->add('type', FormType::CHOICE, [
                'choices' => $choices,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter input prices']),
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

    /**
     * @return array
     */
    private function getInputPriceTypesLabels()
    {
        return [
            PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT => t('Excluding VAT'),
            PricingSetting::INPUT_PRICE_TYPE_WITH_VAT => t('Including VAT'),
        ];
    }
}
