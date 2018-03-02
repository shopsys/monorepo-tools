<?php

namespace Shopsys\FrameworkBundle\Model\Transport\Detail;

use Shopsys\FrameworkBundle\Model\Transport\Transport;
use Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation;

class TransportDetailFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation
     */
    private $transportPriceCalculation;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation $transportPriceCalculation
     */
    public function __construct(
        TransportPriceCalculation $transportPriceCalculation
    ) {
        $this->transportPriceCalculation = $transportPriceCalculation;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     * @return \Shopsys\FrameworkBundle\Model\Transport\Detail\TransportDetail
     */
    public function createDetailForTransportWithIndependentPrices(Transport $transport)
    {
        return new TransportDetail(
            $transport,
            $this->getIndependentPrices($transport)
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport[] $transports
     * @return \Shopsys\FrameworkBundle\Model\Transport\Detail\TransportDetail[]
     */
    public function createDetailsForTransportsWithIndependentPrices(array $transports)
    {
        $details = [];

        foreach ($transports as $transport) {
            $details[] = new TransportDetail(
                $transport,
                $this->getIndependentPrices($transport)
            );
        }

        return $details;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price[]
     */
    private function getIndependentPrices(Transport $transport)
    {
        $prices = [];
        foreach ($transport->getPrices() as $transportInputPrice) {
            $currency = $transportInputPrice->getCurrency();
            $prices[$currency->getId()] = $this->transportPriceCalculation->calculateIndependentPrice($transport, $currency);
        }

        return $prices;
    }
}
