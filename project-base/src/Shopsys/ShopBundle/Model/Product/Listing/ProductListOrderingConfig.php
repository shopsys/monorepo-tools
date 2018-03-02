<?php

namespace Shopsys\FrameworkBundle\Model\Product\Listing;

class ProductListOrderingConfig
{
    /**
     * @var string[]
     */
    private $supportedOrderingModesNamesById;

    /**
     * @var string
     */
    private $defaultOrderingModeId;

    /**
     * @var string
     */
    private $cookieName;

    /**
     * @param string[] $supportedOrderingModesNamesById
     * @param string $defaultOrderingModeId
     * @param string $cookieName
     */
    public function __construct($supportedOrderingModesNamesById, $defaultOrderingModeId, $cookieName)
    {
        $this->supportedOrderingModesNamesById = $supportedOrderingModesNamesById;
        $this->defaultOrderingModeId = $defaultOrderingModeId;
        $this->cookieName = $cookieName;
    }

    /**
     * @return string[]
     */
    public function getSupportedOrderingModesNamesIndexedById()
    {
        return $this->supportedOrderingModesNamesById;
    }

    /**
     * @return string
     */
    public function getCookieName()
    {
        return $this->cookieName;
    }

    /**
     * @return string
     */
    public function getDefaultOrderingModeId()
    {
        return $this->defaultOrderingModeId;
    }
}
