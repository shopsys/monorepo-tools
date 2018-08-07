<?php

namespace Shopsys\FrameworkBundle\Model\Order;

class OrderNumberSequenceFactory implements OrderNumberSequenceFactoryInterface
{
    /**
     * @param int $id
     * @param string $number
     * @return \Shopsys\FrameworkBundle\Model\Order\OrderNumberSequence
     */
    public function create(int $id, string $number): OrderNumberSequence
    {
        return new OrderNumberSequence($id, $number);
    }
}
