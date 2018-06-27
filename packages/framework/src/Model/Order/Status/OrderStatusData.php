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
}
