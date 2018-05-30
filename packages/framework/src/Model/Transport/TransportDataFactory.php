<?php

namespace Shopsys\FrameworkBundle\Model\Transport;

use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade;

class TransportDataFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportFacade
     */
    protected $transportFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade
     */
    protected $vatFacade;

    public function __construct(
        TransportFacade $transportFacade,
        VatFacade $vatFacade
    ) {
        $this->transportFacade = $transportFacade;
        $this->vatFacade = $vatFacade;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Transport\TransportData
     */
    public function createDefault()
    {
        $transportData = new TransportData();
        $transportData->vat = $this->vatFacade->getDefaultVat();

        return $transportData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     * @return \Shopsys\FrameworkBundle\Model\Transport\TransportData
     */
    public function createFromTransport(Transport $transport)
    {
        $transportData = new TransportData();
        $transportData->setFromEntity($transport, $this->transportFacade->getTransportDomainsByTransport($transport));

        foreach ($transport->getPrices() as $transportPrice) {
            $transportData->pricesByCurrencyId[$transportPrice->getCurrency()->getId()] = $transportPrice->getPrice();
        }

        return $transportData;
    }
}
