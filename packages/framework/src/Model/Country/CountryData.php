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

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\Country $country
     */
    public function setFromEntity(Country $country)
    {
        $this->name = $country->getName();
        $this->code = $country->getCode();
    }
}
