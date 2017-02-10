<?php

namespace Shopsys\ShopBundle\Model\Country;

class CountryData
{

    /**
     * @var string
     */
    public $name;

    /**
     * @param string $name
     */
    public function __construct($name = '') {
        $this->name = $name;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Country\Country $country
     */
    public function setFromEntity(Country $country) {
        $this->name = $country->getName();
    }

}
