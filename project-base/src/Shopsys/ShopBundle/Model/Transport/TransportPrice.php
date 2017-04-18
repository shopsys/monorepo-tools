<?php

namespace Shopsys\ShopBundle\Model\Transport;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\ShopBundle\Model\Pricing\Currency\Currency;
use Shopsys\ShopBundle\Model\Transport\Transport;

/**
 * @ORM\Table(name="transport_prices")
 * @ORM\Entity
 */
class TransportPrice
{
    /**
     * @var \Shopsys\ShopBundle\Model\Transport\Transport
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Transport\Transport", inversedBy="prices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $transport;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Currency\Currency
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Pricing\Currency\Currency")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", precision=20, scale=6)
     */
    private $price;

    /**
     * @param \Shopsys\ShopBundle\Model\Transport\Transport $transport
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     * @param string $price
     */
    public function __construct(Transport $transport, Currency $currency, $price)
    {
        $this->transport = $transport;
        $this->currency = $currency;
        $this->price = $price;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Currency\Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Transport\Transport
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param string $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }
}
