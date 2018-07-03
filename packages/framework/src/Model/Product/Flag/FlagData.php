<?php

namespace Shopsys\FrameworkBundle\Model\Product\Flag;

class FlagData
{
    /**
     * @var string[]
     */
    public $name;

    /**
     * @var string|null
     */
    public $rgbColor;

    /**
     * @var bool
     */
    public $visible;

    public function __construct()
    {
        $this->name = [];
        $this->visible = false;
    }
}
