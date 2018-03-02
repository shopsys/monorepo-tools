<?php

namespace Shopsys\FrameworkBundle\Model\Transport\Detail;

use Shopsys\FrameworkBundle\Model\Transport\Transport;

class TransportDetail
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\Transport
     */
    private $transport;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Price[]
     */
    private $basePricesByCurrencyId;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price[] $basePricesByCurrencyId
     */
    public function __construct(
        Transport $transport,
        array $basePricesByCurrencyId
    ) {
        $this->transport = $transport;
        $this->basePricesByCurrencyId = $basePricesByCurrencyId;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Transport\Transport
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price[]
     */
    public function getBasePricesByCurrencyId()
    {
        return $this->basePricesByCurrencyId;
    }
}
