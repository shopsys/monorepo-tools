<?php

namespace Shopsys\FrameworkBundle\Model\Product\Unit;

class UnitData
{
    /**
     * @var string[]|null[]
     */
    public $name;

    public function __construct()
    {
        $this->name = [];
    }
}
