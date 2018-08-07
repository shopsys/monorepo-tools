<?php

namespace Shopsys\FrameworkBundle\Model\Transport;

use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;

class TransportPriceFactory implements TransportPriceFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @param string $price
     * @return \Shopsys\FrameworkBundle\Model\Transport\TransportPrice
     */
    public function create(
        Transport $transport,
        Currency $currency,
        string $price
    ): TransportPrice {
        return new TransportPrice($transport, $currency, $price);
    }
}
