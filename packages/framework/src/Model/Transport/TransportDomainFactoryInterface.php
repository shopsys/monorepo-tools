<?php

namespace Shopsys\FrameworkBundle\Model\Transport;

interface TransportDomainFactoryInterface
{

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Transport\TransportDomain
     */
    public function create(Transport $transport, int $domainId): TransportDomain;
}
