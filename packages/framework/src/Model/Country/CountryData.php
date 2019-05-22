<?php

namespace Shopsys\FrameworkBundle\Model\Country;

class CountryData
{
    /**
     * @var string[]|null[]
     */
    public $names;

    /**
     * @var string|null
     */
    public $code;

    /**
     * @var bool[]
     */
    public $enabled;

    /**
     * @var int[]|null[]
     */
    public $priority;

    public function __construct()
    {
        $this->names = [];
        $this->enabled = [];
        $this->priority = [];
    }
}
