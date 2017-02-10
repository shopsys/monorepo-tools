<?php

namespace Shopsys\ShopBundle\Component\Grid\Ordering;

interface OrderableEntityInterface
{
    /**
     * @param int $position
     */
    public function setPosition($position);
}
