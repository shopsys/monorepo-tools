<?php

namespace Shopsys\FrameworkBundle\Model\Order\Status;

class OrderStatusData
{
    /**
     * @var string[]
     */
    public $name;

    public function __construct()
    {
        $this->name = [];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus $orderStatus
     */
    public function setFromEntity(OrderStatus $orderStatus)
    {
        $translations = $orderStatus->getTranslations();
        $names = [];
        foreach ($translations as $translate) {
            $names[$translate->getLocale()] = $translate->getName();
        }
        $this->name = $names;
    }
}
