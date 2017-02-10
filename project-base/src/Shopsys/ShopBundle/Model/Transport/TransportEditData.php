<?php

namespace Shopsys\ShopBundle\Model\Transport;

use Shopsys\ShopBundle\Model\Transport\TransportData;

class TransportEditData
{
    /**
     * @var \Shopsys\ShopBundle\Model\Transport\TransportData
     */
    public $transportData;

    /**
     * @var string[currencyId]
     */
    public $prices;

    /**
     * @param \Shopsys\ShopBundle\Model\Transport\TransportData $transportData
     * @param array $prices
     */
    public function __construct(TransportData $transportData = null, array $prices = [])
    {
        if ($transportData !== null) {
            $this->transportData = $transportData;
        } else {
            $this->transportData = new TransportData();
        }
        $this->prices = $prices;
    }
}
