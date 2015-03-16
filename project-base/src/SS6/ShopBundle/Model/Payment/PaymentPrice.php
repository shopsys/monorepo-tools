<?php

namespace SS6\ShopBundle\Model\Payment;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;

/**
 * @ORM\Table(name="payment_prices")
 * @ORM\Entity
 */
class PaymentPrice {

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment
	 *
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Payment\Payment", inversedBy="prices")
	 */
	private $payment;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\Currency
	 *
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Pricing\Currency\Currency")
	 */
	private $currency;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="decimal", precision=20, scale=6)
	 */
	private $price;

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param \SS6\ShopBundle\Model\Pricing\Currency $currency
	 * @param string $price
	 */
	public function __construct(Payment $payment, Currency $currency, $price) {
		$this->payment = $payment;
		$this->currency = $currency;
		$this->price = $price;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Currency
	 */
	public function getCurrency() {
		return $this->currency;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Payment\Payment
	 */
	public function getPayment() {
		return $this->payment;
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
