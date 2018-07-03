<?php

namespace Shopsys\FrameworkBundle\Model\Product\Unit;

class UnitData
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
