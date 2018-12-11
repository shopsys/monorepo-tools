<?php

namespace Shopsys\FrameworkBundle\Model\Order;

class OrderEditResult
{
    /**
     * @var bool
     */
    private $statusChanged;

    /**
     * @param bool $statusChanged
     */
    public function __construct(bool $statusChanged)
    {
        $this->statusChanged = $statusChanged;
    }

    /**
     * @return bool
     */
    public function isStatusChanged()
    {
        return $this->statusChanged;
    }
}
