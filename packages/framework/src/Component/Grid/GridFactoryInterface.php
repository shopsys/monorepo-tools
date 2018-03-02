<?php

namespace Shopsys\FrameworkBundle\Component\Grid;

interface GridFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Component\Grid\Grid
     */
    public function create();
}
