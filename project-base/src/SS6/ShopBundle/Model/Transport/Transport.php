<?php

namespace SS6\ShopBundle\Model\Transport;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use SS6\ShopBundle\Model\Grid\Ordering\OrderableEntityInterface;
use SS6\ShopBundle\Model\Localization\AbstractTranslatableEntity;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Transport\TransportData;

/**
 * @ORM\Table(name="transports")
 * @ORM\Entity
 */
class Transport extends AbstractTranslatableEntity implements OrderableEntityInterface {

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportTranslation[]
	 *
	 * @Prezent\Translations(targetEntity="SS6\ShopBundle\Model\Transport\TransportTranslation")
	 */
	protected $translations;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportPrice[]
	 *
	 * @ORM\OneToMany(targetEntity="SS6\ShopBundle\Model\Transport\TransportPrice", mappedBy="transport", cascade={"persist"})
	 */
	private $prices;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Pricing\Vat\Vat")
	 */
	private $vat;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $hidden;

	/**
	 * @var int
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $deleted;

	/**
	 * @var int|null
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $position;

	/**
	 * @param \SS6\ShopBundle\Model\Transport\TransportData $transportData
	 */
	public function __construct(TransportData $transportData) {
		$this->translations = new ArrayCollection();
		$this->vat = $transportData->vat;
		$this->hidden = $transportData->hidden;
		$this->deleted = false;
		$this->setTranslations($transportData);
		$this->prices = new ArrayCollection();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\TransportData $transportData
	 */
	public function edit(TransportData $transportData) {
		$this->vat = $transportData->vat;
		$this->hidden = $transportData->hidden;
		$this->setTranslations($transportData);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\TransportData $transportData
	 */
	private function setTranslations(TransportData $transportData) {
		foreach ($transportData->name as $locale => $name) {
			$this->translation($locale)->setName($name);
		}
		foreach ($transportData->description as $locale => $description) {
			$this->translation($locale)->setDescription($description);
		}
		foreach ($transportData->instructions as $locale => $instructions) {
			$this->translation($locale)->setInstructions($instructions);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 */
	public function changeVat(Vat $vat) {
		$this->vat = $vat;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string|null $locale
	 * @return string
	 */
	public function getName($locale = null) {
		return $this->translation($locale)->getName();
	}

	/**
	 * @param string|null $locale
	 * @return string|null
	 */
	public function getDescription($locale = null) {
		return $this->translation($locale)->getDescription();
	}

	/**
	 * @param string|null $locale
	 * @return string|null
	 */
	public function getInstructions($locale = null) {
		return $this->translation($locale)->getInstructions();
	}

	/*
	 * @return \SS6\ShopBundle\Model\Transport\TransportPrice[]
	 */
	public function getPrices() {
		return $this->prices;
	}

	/*
	 * @return \SS6\ShopBundle\Model\Transport\TransportPrice
	 */
	public function getPrice(Currency $currency) {
		foreach ($this->prices as $price) {
			if ($price->getCurrency() === $currency) {
				return $price;
			}
		}

		$message = 'Transport price with currency ID ' . $currency->getId()
			. ' from transport with ID ' . $this->getId() . 'not found.';
		throw new \SS6\ShopBundle\Model\Transport\Exception\TransportPriceNotFoundException($message);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @param string $price
	 */
	public function setPrice(Currency $currency, $price) {
		foreach ($this->prices as $transportInputPrice) {
			if ($transportInputPrice->getCurrency() === $currency) {
				$transportInputPrice->setPrice($price);
				return;
			}
		}

		$this->prices[] = new TransportPrice($this, $currency, $price);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 */
	public function getVat() {
		return $this->vat;
	}

	/**
	 * @return bool
	 */
	public function isHidden() {
		return $this->hidden;
	}

	/**
	 * @return bool
	 */
	public function isDeleted() {
		return $this->deleted;
	}

	/**
	 * @param bool $deleted
	 */
	public function markAsDeleted() {
		$this->deleted = true;
	}

	/**
	 * @return int|null
	 */
	public function getPosition() {
		return $this->position;
	}

	/**
	 * @param int $position
	 */
	public function setPosition($position) {
		$this->position = $position;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Transport\TransportTranslation
	 */
	protected function createTranslation() {
		return new TransportTranslation();
	}
}
