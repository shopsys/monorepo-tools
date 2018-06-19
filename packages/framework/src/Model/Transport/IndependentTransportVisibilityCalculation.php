<?php

namespace Shopsys\FrameworkBundle\Model\Transport;

use Shopsys\FrameworkBundle\Component\Domain\Domain;

class IndependentTransportVisibilityCalculation
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportRepository
     */
    private $transportRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(
        TransportRepository $transportRepository,
        Domain $domain
    ) {
        $this->transportRepository = $transportRepository;
        $this->domain = $domain;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     * @param int $domainId
     * @return bool
     */
    public function isIndependentlyVisible(Transport $transport, $domainId)
    {
        $locale = $this->domain->getDomainConfigById($domainId)->getLocale();

        if (strlen($transport->getName($locale)) === 0) {
            return false;
        }

        if ($transport->isHidden()) {
            return false;
        }

        if (!$transport->isEnabled($domainId)) {
            return false;
        }

        return true;
    }
}
