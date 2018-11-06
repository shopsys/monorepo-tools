<?php

namespace Shopsys\FrameworkBundle\Model\Transport;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade;

class TransportDataFactory implements TransportDataFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportFacade
     */
    protected $transportFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade
     */
    protected $vatFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\TransportFacade $transportFacade
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade $vatFacade
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(
        TransportFacade $transportFacade,
        VatFacade $vatFacade,
        Domain $domain
    ) {
        $this->transportFacade = $transportFacade;
        $this->vatFacade = $vatFacade;
        $this->domain = $domain;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Transport\TransportData
     */
    public function create(): TransportData
    {
        $transportData = new TransportData();
        $this->fillNew($transportData);

        return $transportData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\TransportData $transportData
     */
    protected function fillNew(TransportData $transportData)
    {
        $transportData->vat = $this->vatFacade->getDefaultVat();

        foreach ($this->domain->getAllIds() as $domainId) {
            $transportData->enabled[$domainId] = true;
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     * @return \Shopsys\FrameworkBundle\Model\Transport\TransportData
     */
    public function createFromTransport(Transport $transport): TransportData
    {
        $transportData = new TransportData();
        $this->fillFromTransport($transportData, $transport);

        return $transportData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\TransportData $transportData
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     */
    protected function fillFromTransport(TransportData $transportData, Transport $transport)
    {
        $names = [];
        $descriptions = [];
        $instructions = [];

        $translations = $transport->getTranslations();

        foreach ($translations as $translate) {
            $names[$translate->getLocale()] = $translate->getName();
            $descriptions[$translate->getLocale()] = $translate->getDescription();
            $instructions[$translate->getLocale()] = $translate->getInstructions();
        }

        $transportData->name = $names;
        $transportData->description = $descriptions;
        $transportData->instructions = $instructions;
        $transportData->hidden = $transport->isHidden();
        $transportData->vat = $transport->getVat();

        foreach ($this->domain->getAllIds() as $domainId) {
            $transportData->enabled[$domainId] = $transport->isEnabled($domainId);
        }

        $transportData->payments = $transport->getPayments()->toArray();

        foreach ($transport->getPrices() as $transportPrice) {
            $transportData->pricesByCurrencyId[$transportPrice->getCurrency()->getId()] = $transportPrice->getPrice();
        }
    }
}
