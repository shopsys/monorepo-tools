<?php

namespace Shopsys\FrameworkBundle\Model\Product\Listing;

class ProductListOrderingConfig
{
    public const ORDER_BY_PRIORITY = 'priority';
    public const ORDER_BY_PRICE_DESC = 'price_desc';
    public const ORDER_BY_PRICE_ASC = 'price_asc';
    public const ORDER_BY_NAME_DESC = 'name_desc';
    public const ORDER_BY_RELEVANCE = 'relevance';
    public const ORDER_BY_NAME_ASC = 'name_asc';

    /**
     * @var string[]
     */
    protected $supportedOrderingModesNamesById;

    /**
     * @var string
     */
    protected $defaultOrderingModeId;

    /**
     * @var string
     */
    protected $cookieName;

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

    /**
     * @return string[]
     */
    public function getSupportedOrderingModeIds()
    {
        return array_keys($this->supportedOrderingModesNamesById);
    }
}
