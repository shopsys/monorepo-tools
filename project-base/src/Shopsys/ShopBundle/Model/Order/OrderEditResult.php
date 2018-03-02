<?php

namespace Shopsys\FrameworkBundle\Model\Order;

class OrderEditResult
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem[]
     */
    private $orderItemsToCreate;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem[]
     */
    private $orderItemsToDelete;

    /**
     * @var bool
     */
    private $statusChanged;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem[] $orderItemsToCreate
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem[] $orderItemsToDelete
     * @param $statusChanged
     */
    public function __construct(array $orderItemsToCreate, array $orderItemsToDelete, $statusChanged)
    {
        $this->orderItemsToCreate = $orderItemsToCreate;
        $this->orderItemsToDelete = $orderItemsToDelete;
        $this->statusChanged = $statusChanged;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem[]
     */
    public function getOrderItemsToCreate()
    {
        return $this->orderItemsToCreate;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem[]
     */
    public function getOrderItemsToDelete()
    {
        return $this->orderItemsToDelete;
    }

    /**
     * @return bool
     */
    public function isStatusChanged()
    {
        return $this->statusChanged;
    }
}
