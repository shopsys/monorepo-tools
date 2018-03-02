<?php

namespace Shopsys\FrameworkBundle\Model\Transport;

class TransportEditData
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportData
     */
    public $transportData;

    /**
     * @var string[]
     */
    public $pricesByCurrencyId;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\TransportData $transportData
     * @param array $pricesByCurrencyId
     */
    public function __construct(TransportData $transportData = null, array $pricesByCurrencyId = [])
    {
        if ($transportData !== null) {
            $this->transportData = $transportData;
        } else {
            $this->transportData = new TransportData();
        }
        $this->pricesByCurrencyId = $pricesByCurrencyId;
    }
}
