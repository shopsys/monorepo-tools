<?php

namespace Shopsys\ShopBundle\Form\Admin\Pricing\Currency;

use Shopsys\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class CurrencySettingsFormType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Currency\Currency[]
     */
    private $currencies;

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency[] $currencies
     */
    public function __construct(array $currencies)
    {
        $this->currencies = $currencies;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'currency_settings_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('defaultCurrency', FormType::CHOICE, [
                'required' => true,
                'choice_list' => new ObjectChoiceList($this->currencies, 'name', [], null, 'id'),
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter default currency']),
                ],
            ])
            ->add('domainDefaultCurrencies', FormType::COLLECTION, [
                'required' => true,
                'type' => 'choice',
                'options' => [
                    'required' => true,
                    'choice_list' => new ObjectChoiceList($this->currencies, 'name', [], null, 'id'),
                    'constraints' => [
                        new Constraints\NotBlank(['message' => 'Please enter default currency']),
                    ],
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
