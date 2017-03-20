<?php

namespace Shopsys\ShopBundle\Form\Admin\Pricing\Currency;

use Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class CurrencySettingsFormType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    public function __construct(CurrencyFacade $currencyFacade)
    {
        $this->currencyFacade = $currencyFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('defaultCurrency', ChoiceType::class, [
                'required' => true,
                'choice_list' => new ObjectChoiceList($this->currencyFacade->getAll(), 'name', [], null, 'id'),
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter default currency']),
                ],
            ])
            ->add('domainDefaultCurrencies', CollectionType::class, [
                'required' => true,
                'entry_type' => ChoiceType::class,
                'options' => [
                    'required' => true,
                    'choice_list' => new ObjectChoiceList($this->currencyFacade->getAll(), 'name', [], null, 'id'),
                    'constraints' => [
                        new Constraints\NotBlank(['message' => 'Please enter default currency']),
                    ],
                ],
            ])
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
