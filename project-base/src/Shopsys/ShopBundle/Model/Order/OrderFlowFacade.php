<?php

namespace Shopsys\ShopBundle\Model\Order;

use Shopsys\ShopBundle\Form\Front\Order\OrderFlow;
use Shopsys\ShopBundle\Model\Country\CountryFacade;
use Shopsys\ShopBundle\Model\Payment\PaymentFacade;
use Shopsys\ShopBundle\Model\Transport\TransportFacade;

class OrderFlowFacade
{
    /**
     * @var \Shopsys\ShopBundle\Form\Front\Order\OrderFlow
     */
    private $orderFlow;

    /**
     * @var \Shopsys\ShopBundle\Model\Transport\TransportFacade
     */
    private $transportFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Payment\PaymentFacade
     */
    private $paymentFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Country\CountryFacade
     */
    private $countryFacade;

    /**
     * @param \Shopsys\ShopBundle\Form\Front\Order\OrderFlow $orderFlow
     * @param \Shopsys\ShopBundle\Model\Payment\PaymentFacade $paymentFacade
     * @param \Shopsys\ShopBundle\Model\Transport\TransportFacade $transportFacade
     * @param \Shopsys\ShopBundle\Model\Country\CountryFacade $countryFacade
     */
    public function __construct(
        OrderFlow $orderFlow,
        PaymentFacade $paymentFacade,
        TransportFacade $transportFacade,
        CountryFacade $countryFacade
    ) {
        $this->orderFlow = $orderFlow;
        $this->paymentFacade = $paymentFacade;
        $this->transportFacade = $transportFacade;
        $this->countryFacade = $countryFacade;
    }

    public function resetOrderForm()
    {
        $payments = $this->paymentFacade->getVisibleOnCurrentDomain();
        $transports = $this->transportFacade->getVisibleOnCurrentDomain($payments);
        $countries = $this->countryFacade->getAllOnCurrentDomain();
        $this->orderFlow->setFormTypesData($transports, $payments, $countries);
        $this->orderFlow->reset();
    }
}
