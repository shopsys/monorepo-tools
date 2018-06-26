<?php

namespace Shopsys\FrameworkBundle\Model\Country;

class CountryData
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string|null
     */
    public $code;

    public function __construct()
    {
        $this->name = '';
    }
}
