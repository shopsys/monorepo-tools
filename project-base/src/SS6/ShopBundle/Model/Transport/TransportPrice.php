<?php

namespace SS6\ShopBundle\Model\Transport;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Transport\Transport;

/**
 * @ORM\Table(name="transport_prices")
 * @ORM\Entity
 */
class TransportPrice {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport
	 *
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Transport\Transport", inversedBy="prices")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $transport;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\Currency
	 *
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Pricing\Currency\Currency")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $currency;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="decimal", precision=20, scale=6)
	 */
	private $price;

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @param string $price
	 */
	public function __construct(Transport $transport, Currency $currency, $price) {
		$this->transport = $transport;
		$this->currency = $currency;
		$this->price = $price;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Currency\Currency
	 */
	public function getCurrency() {
		return $this->currency;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Transport\Transport
	 */
	public function getTransport() {
		return $this->transport;
	}

	/**
	 * @return string
	 */
	public function getPrice() {
		return $this->price;
	}

	/**
	 * @param string $price
	 */
	public function setPrice($price) {
		$this->price = $price;
	}

}
