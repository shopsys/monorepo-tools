<?php

namespace Shopsys\ShopBundle\Model\Payment;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Shopsys\ShopBundle\Component\Gedmo\SortablePosition;
use Shopsys\ShopBundle\Component\Grid\Ordering\OrderableEntityInterface;
use Shopsys\ShopBundle\Model\Localization\AbstractTranslatableEntity;
use Shopsys\ShopBundle\Model\Payment\PaymentData;
use Shopsys\ShopBundle\Model\Pricing\Currency\Currency;
use Shopsys\ShopBundle\Model\Transport\Transport;

/**
 * @ORM\Table(name="payments")
 * @ORM\Entity
 */
class Payment extends AbstractTranslatableEntity implements OrderableEntityInterface {

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @var \Shopsys\ShopBundle\Model\Payment\PaymentTranslation[]
	 *
	 * @Prezent\Translations(targetEntity="Shopsys\ShopBundle\Model\Payment\PaymentTranslation")
	 */
	protected $translations;

	/**
	 * @var \Shopsys\ShopBundle\Model\Payment\PaymentPrice[]
	 *
	 * @ORM\OneToMany(targetEntity="Shopsys\ShopBundle\Model\Payment\PaymentPrice", mappedBy="payment", cascade={"persist"})
	 */
	private $prices;

	/**
	 * @var \Shopsys\ShopBundle\Model\Pricing\Vat\Vat
	 *
	 * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Pricing\Vat\Vat")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $vat;

	/**
	 * @var Collection
	 *
	 * @ORM\ManyToMany(targetEntity="Shopsys\ShopBundle\Model\Transport\Transport")
	 * @ORM\JoinTable(name="payments_transports")
	 */
	private $transports;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $hidden;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $deleted;

	/**
	 * @var int|null
	 *
	 * @Gedmo\SortablePosition
	 * @ORM\Column(type="integer", nullable=false)
	 */
	private $position;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $czkRounding;

	/**
	 * @param \Shopsys\ShopBundle\Model\Payment\PaymentData $paymentData
	 */
	public function __construct(PaymentData $paymentData) {
		$this->translations = new ArrayCollection();
		$this->vat = $paymentData->vat;
		$this->transports = new ArrayCollection();
		$this->hidden = $paymentData->hidden;
		$this->deleted = false;
		$this->setTranslations($paymentData);
		$this->prices = new ArrayCollection();
		$this->czkRounding = $paymentData->czkRounding;
		$this->position = SortablePosition::LAST_POSITION;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Transport\Transport $transport
	 */
	public function addTransport(Transport $transport) {
		if (!$this->transports->contains($transport)) {
			$this->transports->add($transport);
		}
	}

	/**
	 * @param array $transports
	 */
	public function setTransports(array $transports) {
		$this->transports->clear();
		foreach ($transports as $transport) {
			/* @var $transport \Shopsys\ShopBundle\Model\Transport\Transport */
			$this->addTransport($transport);
		}
	}

	/**
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getTransports() {
		return $this->transports;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Payment\PaymentData $paymentData
	 */
	private function setTranslations(PaymentData $paymentData) {
		foreach ($paymentData->name as $locale => $name) {
			$this->translation($locale)->setName($name);
		}
		foreach ($paymentData->description as $locale => $description) {
			$this->translation($locale)->setDescription($description);
		}
		foreach ($paymentData->instructions as $locale => $instructions) {
			$this->translation($locale)->setInstructions($instructions);
		}
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Payment\PaymentData $paymentData
	 */
	public function edit(PaymentData $paymentData) {
		$this->vat = $paymentData->vat;
		$this->hidden = $paymentData->hidden;
		$this->czkRounding = $paymentData->czkRounding;
		$this->setTranslations($paymentData);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @param string $price
	 */
	public function setPrice(Currency $currency, $price) {
		foreach ($this->prices as $paymentInputPrice) {
			if ($paymentInputPrice->getCurrency() === $currency) {
				$paymentInputPrice->setPrice($price);
				return;
			}
		}

		$this->prices[] = new PaymentPrice($this, $currency, $price);
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

	/*
	 * @return \Shopsys\ShopBundle\Model\Payment\PaymentPrice[]
	 */
	public function getPrices() {
		return $this->prices;
	}

	/*
	 * @return \Shopsys\ShopBundle\Model\Payment\PaymentPrice
	 */
	public function getPrice(Currency $currency) {
		foreach ($this->prices as $price) {
			if ($price->getCurrency() === $currency) {
				return $price;
			}
		}

		$message = 'Payment price with currency ID ' . $currency->getId() . ' from payment with ID ' . $this->getId() . 'not found.';
		throw new \Shopsys\ShopBundle\Model\Payment\Exception\PaymentPriceNotFoundException($message);
	}

	/**
	 * @return \Shopsys\ShopBundle\Model\Pricing\Vat\Vat
	 */
	public function getVat() {
		return $this->vat;
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
		$this->transports->clear();
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
	 * @return bool
	 */
	public function isCzkRounding() {
		return $this->czkRounding;
	}

	/**
	 * @return \Shopsys\ShopBundle\Model\Payment\PaymentTranslation
	 */
	protected function createTranslation() {
		return new PaymentTranslation();
	}

}
