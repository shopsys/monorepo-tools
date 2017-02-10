<?php

namespace Shopsys\ShopBundle\Form\Admin\Payment;

use Shopsys\ShopBundle\Form\Admin\Payment\PaymentFormTypeFactory;
use Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade;

class PaymentEditFormTypeFactory
{
    /**
     * @var \Shopsys\ShopBundle\Form\Admin\Payment\PaymentFormTypeFactory
     */
    private $paymentFormTypeFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    public function __construct(PaymentFormTypeFactory $paymentFormTypeFactory, CurrencyFacade $currencyFacade)
    {
        $this->paymentFormTypeFactory = $paymentFormTypeFactory;
        $this->currencyFacade = $currencyFacade;
    }

    /**
     * @return \Shopsys\ShopBundle\Form\Admin\Payment\PaymentFormType
     */
    public function create()
    {
        $currencies = $this->currencyFacade->getAll();

        return new PaymentEditFormType($this->paymentFormTypeFactory, $currencies);
    }
}
