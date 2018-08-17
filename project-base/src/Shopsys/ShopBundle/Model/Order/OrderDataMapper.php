<?php

namespace Shopsys\ShopBundle\Model\Order;

use Shopsys\FrameworkBundle\Model\Order\FrontOrderData as BaseFrontOrderData;
use Shopsys\FrameworkBundle\Model\Order\OrderDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Order\OrderDataMapper as BaseOrderDataMapper;

class OrderDataMapper extends BaseOrderDataMapper
{
    public function __construct(OrderDataFactoryInterface $orderDataFactory)
    {
        parent::__construct($orderDataFactory);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\FrontOrderData $frontOrderData
     * @return \Shopsys\ShopBundle\Model\Order\OrderData
     */
    public function getOrderDataFromFrontOrderData(BaseFrontOrderData $frontOrderData)
    {
        /** @var \Shopsys\ShopBundle\Model\Order\OrderData $orderData */
        $orderData = parent::getOrderDataFromFrontOrderData($frontOrderData);

        return $orderData;
    }
}
