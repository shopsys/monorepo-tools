<?php

namespace Shopsys\ShopBundle\Model\Order;

use Shopsys\ShopBundle\Component\Domain\Domain;
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
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @param \Shopsys\ShopBundle\Form\Front\Order\OrderFlow $orderFlow
     * @param \Shopsys\ShopBundle\Component\Domain\Domain $domain
     */
    public function __construct(
        OrderFlow $orderFlow,
        Domain $domain
    ) {
        $this->orderFlow = $orderFlow;
        $this->domain = $domain;
    }

    public function resetOrderForm()
    {
        $this->orderFlow->setDomainId($this->domain->getId());
        $this->orderFlow->reset();
    }
}
