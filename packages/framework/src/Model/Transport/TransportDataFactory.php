<?php

namespace Shopsys\FrameworkBundle\Model\Transport;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
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

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

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
    public function createDefault()
    {
        $transportData = new TransportData();
        $transportData->vat = $this->vatFacade->getDefaultVat();

        foreach ($this->domain->getAllIds() as $domainId) {
            $transportData->enabled[$domainId] = true;
        }

        return $transportData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     * @return \Shopsys\FrameworkBundle\Model\Transport\TransportData
     */
    public function createFromTransport(Transport $transport)
    {
        $transportData = $this->createDefault();

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

        return $transportData;
    }
}
