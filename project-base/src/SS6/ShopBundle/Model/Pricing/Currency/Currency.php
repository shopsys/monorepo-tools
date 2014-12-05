<?php

namespace SS6\ShopBundle\Model\Pricing\Currency;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="currencies")
 * @ORM\Entity
 */
class Currency {

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=50)
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=3)
	 */
	private $code;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=10)
	 */
	private $symbol;


	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\CurrencyData $currencyData
	 */
	public function __construct(CurrencyData $currencyData) {
		$this->name = $currencyData->getName();
		$this->code = $currencyData->getCode();
		$this->symbol = $currencyData->getSymbol();
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * @return string
	 */
	public function getSymbol() {
		return $this->symbol;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\CurrencyData $currencyData
	 */
	public function edit(CurrencyData $currencyData) {
		$this->name = $currencyData->getName();
		$this->code = $currencyData->getCode();
		$this->symbol = $currencyData->getSymbol();
	}

}
