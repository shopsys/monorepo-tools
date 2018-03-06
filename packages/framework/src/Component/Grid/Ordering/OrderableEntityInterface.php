<?php

namespace Shopsys\FrameworkBundle\Component\Grid\Ordering;

interface OrderableEntityInterface
{
    /**
     * @param int $position
     */
    public function setPosition($position);
}
