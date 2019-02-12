<?php

namespace Shopsys\FrameworkBundle\Model\Order\Status;

class OrderStatusData
{
    /**
     * @var string[]|null[]
     */
    public $name;

    public function __construct()
    {
        $this->name = [];
    }
}
