<?php

namespace Shopsys\ShopBundle\Form\Admin\Payment;

use Shopsys\ShopBundle\Model\Payment\PaymentEditData;
use Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class PaymentEditFormType extends AbstractType
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
            ->add('paymentData', PaymentFormType::class)
            ->add($this->getPricesBuilder($builder))
            ->add('save', SubmitType::class);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    private function getPricesBuilder(FormBuilderInterface $builder)
    {
        $pricesBuilder = $builder->create('pricesByCurrencyId', null, [
            'compound' => true,
        ]);
        foreach ($this->currencyFacade->getAll() as $currency) {
            $pricesBuilder
                ->add($currency->getId(), MoneyType::class, [
                    'currency' => false,
                    'scale' => 6,
                    'required' => true,
                    'invalid_message' => 'Please enter price in correct format (positive number with decimal separator)',
                    'constraints' => [
                        new Constraints\NotBlank(['message' => 'Please enter price']),
                        new Constraints\GreaterThanOrEqual([
                            'value' => 0,
                            'message' => 'Price must be greater or equal to {{ compared_value }}',
                        ]),

                    ],
                ]);
        }

        return $pricesBuilder;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PaymentEditData::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
