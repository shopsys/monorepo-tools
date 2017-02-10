<?php

namespace Shopsys\ShopBundle\Component\Grid;

interface GridFactoryInterface
{

    /**
     * @return \Shopsys\ShopBundle\Component\Grid\Grid
     */
    public function create();
}
