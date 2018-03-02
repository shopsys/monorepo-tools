<?php

namespace Shopsys\FrameworkBundle\Model\Order;

interface OrderFlowFactoryInterface
{
    /**
     * @return \Craue\FormFlowBundle\Form\FormFlow
     */
    public function create();
}
