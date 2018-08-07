<?php

namespace Shopsys\FrameworkBundle\Model\Order;

interface OrderNumberSequenceFactoryInterface
{
    /**
     * @param int $id
     * @param string $number
     * @return \Shopsys\FrameworkBundle\Model\Order\OrderNumberSequence
     */
    public function create(int $id, string $number): OrderNumberSequence;
}
