<?php

namespace Shopsys\ShopBundle\Model\Transport\Detail;

use Shopsys\ShopBundle\Model\Transport\Transport;

class TransportDetail
{

    /**
     * @var \Shopsys\ShopBundle\Model\Transport\Transport
     */
    private $transport;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Price[currencyId]
     */
    private $basePrices;

    /**
     * @param \Shopsys\ShopBundle\Model\Transport\Transport $transport
     * @param \Shopsys\ShopBundle\Model\Pricing\Price[currencyId] $basePrices
     */
    public function __construct(
        Transport $transport,
        array $basePrices
    ) {
        $this->transport = $transport;
        $this->basePrices = $basePrices;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Transport\Transport
     */
    public function getTransport() {
        return $this->transport;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Price[currencyId]
     */
    public function getBasePrices() {
        return $this->basePrices;
    }
}
