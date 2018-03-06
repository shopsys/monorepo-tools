<?php

namespace Shopsys\FrameworkBundle\Model\Order\Status;

class OrderStatusData
{
    /**
     * @var string[]
     */
    public $name;

    /**
     * @param string[] $name
     */
    public function __construct(array $name = [])
    {
        $this->name = $name;
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
