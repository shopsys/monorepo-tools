<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Form;

use Shopsys\FormTypesBundle\MultidomainType;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Form\Constraints\MoneyRange;
use Shopsys\FrameworkBundle\Form\Transformers\NumericToMoneyTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class HeurekaProductFormType extends AbstractType
{
    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('cpc', MultidomainType::class, [
            'label' => $this->translator->trans('Maximum price per click'),
            'entry_type' => MoneyType::class,
            'required' => false,
            'entry_options' => [
                'currency' => 'CZK',
                'scale' => 2,
                'constraints' => [
                    new MoneyRange([
                        'min' => Money::fromInteger(0),
                        'max' => Money::fromInteger(500),
                    ]),
                ],
            ],
        ]);

        foreach ($builder->get('cpc')->all() as $price) {
            /** @var \Symfony\Component\Form\FormBuilderInterface $price */
            $price->addModelTransformer(new NumericToMoneyTransformer(2));
        }
    }
}
