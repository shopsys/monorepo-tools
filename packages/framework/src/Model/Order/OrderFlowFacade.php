<?php

namespace Shopsys\FrameworkBundle\Model\Order;

class OrderFlowFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderFlowFactoryInterface
     */
    private $orderFlowFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderFlowFactoryInterface $orderFlowFactory
     */
    public function __construct(OrderFlowFactoryInterface $orderFlowFactory)
    {
        $this->orderFlowFactory = $orderFlowFactory;
    }

    public function resetOrderForm()
    {
        $orderFlow = $this->orderFlowFactory->create();
        $orderFlow->reset();
    }
}
