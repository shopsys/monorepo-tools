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

    /**
     * @param string $name
     * @param null $code
     */
    public function __construct($name = '', $code = null)
    {
        $this->name = $name;
        $this->code = $code;
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
