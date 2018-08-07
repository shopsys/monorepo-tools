<?php

namespace Shopsys\FrameworkBundle\Model\Transport;

class TransportFactory implements TransportFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\TransportData $data
     * @return \Shopsys\FrameworkBundle\Model\Transport\Transport
     */
    public function create(TransportData $data): Transport
    {
        return new Transport($data);
    }
}
