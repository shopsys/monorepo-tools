<?php

namespace Shopsys\FrameworkBundle\Model\Transport;

interface TransportFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\TransportData $data
     * @return \Shopsys\FrameworkBundle\Model\Transport\Transport
     */
    public function create(TransportData $data): Transport;
}
