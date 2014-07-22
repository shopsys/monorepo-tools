<?php

namespace SS6\ShopBundle\Model\Order;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Order\Item\OrderItem;
use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Transport\Transport;

/**
 * @ORM\Table(name="orders")
 * @ORM\Entity
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Order {

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
	 * @ORM\Column(type="string", length=30, unique=true, nullable=false)
	 */
	private $number;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\User
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Customer\User")
	 */
	private $customer;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(type="datetime")
	 */
	private $createdAt;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\OrderItem[]
	 *
	 * @ORM\OneToMany(targetEntity="SS6\ShopBundle\Model\Order\Item\OrderItem", mappedBy="order", orphanRemoval=true)
	 */
	private $items;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Transport\Transport")
	 */
	private $transport;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Payment\Payment")
	 */
	private $payment;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Status\OrderStatus
	 *
	 * @ORM\ManyToOne(targetEntity="\SS6\ShopBundle\Model\Order\Status\OrderStatus")
	 */
	private $status;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="decimal", precision=20, scale=6)
	 */
	private $totalPrice;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="decimal", precision=20, scale=6)
	 */
	private $totalProductPrice;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=100)
	 */
	private $firstName;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=100)
	 */
	private $lastName;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=255)
	 */
	private $email;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=30)
	 */
	private $telephone;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $companyName;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $companyNumber;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $companyTaxNumber;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=100)
	 */
	private $street;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=100)
	 */
	private $city;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=30)
	 */
	private $postcode;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $deliveryFirstName;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $deliveryLastName;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $deliveryCompanyName;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=30, nullable=true)
	 */
	private $deliveryTelephone;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $deliveryStreet;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $deliveryCity;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=30, nullable=true)
	 */
	private $deliveryPostcode;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $note;

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $deleted;

	/**
	 * @param string $number
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $email
	 * @param string $telephone
	 * @param string $street
	 * @param string $city
	 * @param string $postcode
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 * @param string|null $note
	 */
	public function __construct(
		$number,
		Transport $transport,
		Payment $payment,
		OrderStatus $orderStatus,
		$firstName,
		$lastName,
		$email,
		$telephone,
		$street,
		$city,
		$postcode,
		User $user = null,
		$note = null
	) {
		$this->number = $number;
		$this->customer = $user;
		$this->items = new ArrayCollection();
		$this->createdAt = new DateTime();
		$this->transport = $transport;
		$this->payment = $payment;
		$this->status = $orderStatus;
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->email = $email;
		$this->telephone = $telephone;
		$this->street = $street;
		$this->city = $city;
		$this->postcode = $postcode;
		$this->note = $note;
		$this->deleted = false;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $email
	 * @param string $telephone
	 * @param string $street
	 * @param string $city
	 * @param string $postcode
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 * @param string|null $companyName
	 * @param string|null $companyNumber
	 * @param string|null $companyTaxNumber
	 * @param string|null $deliveryFirstName
	 * @param string|null $deliveryLastName
	 * @param string|null $deliveryCompanyName
	 * @param string|null $deliveryTelephone
	 * @param string|null $deliveryStreet
	 * @param string|null $deliveryCity
	 * @param string|null $deliveryPostcode
	 * @param string|null $note
	 */
	public function edit(OrderStatus $orderStatus, $firstName, $lastName, $email, $telephone, $street, $city, 
			$postcode, $user, $companyName, $companyNumber, $companyTaxNumber,
			$deliveryFirstName, $deliveryLastName, $deliveryCompanyName, $deliveryTelephone, $deliveryStreet,
			$deliveryCity, $deliveryPostcode, $note) {
		$this->status = $orderStatus;
		$this->customer = $user;
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->email = $email;
		$this->telephone = $telephone;
		$this->companyName = $companyName;
		$this->companyNumber = $companyNumber;
		$this->companyTaxNumber = $companyTaxNumber;
		$this->street = $street;
		$this->city = $city;
		$this->postcode = $postcode;
		$this->deliveryFirstName = $deliveryFirstName;
		$this->deliveryLastName = $deliveryLastName;
		$this->deliveryCompanyName = $deliveryCompanyName;
		$this->deliveryTelephone = $deliveryTelephone;
		$this->deliveryStreet = $deliveryStreet;
		$this->deliveryCity = $deliveryCity;
		$this->deliveryPostcode = $deliveryPostcode;
		$this->note = $note;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItem $item
	 */
	public function addItem(OrderItem $item) {
		if (!$this->items->contains($item)) {
			$this->items->add($item);
		}
		$this->recalcTotalPrices();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItem $item
	 */
	public function removeItem(OrderItem $item) {
		$this->items->removeElement($item);
		$this->recalcTotalPrices();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $status
	 */
	public function setStatus(OrderStatus $status) {
		$this->status = $status;
	}

	/**
	 * @param string|null $companyName
	 * @param string|null $companyNumber
	 * @param string|null $companyTaxNumber
	 */
	public function setCompanyInfo($companyName = null, $companyNumber = null, $companyTaxNumber = null) {
		$this->companyName = $companyName;
		$this->companyNumber = $companyNumber;
		$this->companyTaxNumber = $companyTaxNumber;
	}

	/**
	 * @param string|null $deliveryFirstName
	 * @param string|null $deliveryLastName
	 * @param string|null $deliveryCompanyName
	 * @param string|null $deliveryTelephone
	 * @param string|null $deliveryStreet
	 * @param string|null $deliveryCity
	 * @param string|null $deliveryPostcode
	 */
	public function setDeliveryAddress(
		$deliveryFirstName = null,
		$deliveryLastName = null,
		$deliveryCompanyName = null,
		$deliveryTelephone = null,
		$deliveryStreet = null,
		$deliveryCity = null,
		$deliveryPostcode = null
	) {
		$this->deliveryFirstName = $deliveryFirstName;
		$this->deliveryLastName = $deliveryLastName;
		$this->deliveryCompanyName = $deliveryCompanyName;
		$this->deliveryTelephone = $deliveryTelephone;
		$this->deliveryStreet = $deliveryStreet;
		$this->deliveryCity = $deliveryCity;
		$this->deliveryPostcode = $deliveryPostcode;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Payment\Payment
	 */
	public function getPayment() {
		return $this->payment;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Transport\Transport
	 */
	public function getTransport() {
		return $this->transport;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Order\Status\OrderStatus
	 */
	public function getStatus() {
		return $this->status;
	}

	public function detachCustomer() {
		$this->customer = null;
	}

	/**
	 * @return string
	 */
	public function getTotalPrice() {
		$this->recalcTotalPrices();
		return $this->totalPrice;
	}

	/**
	 * @return string
	 */
	public function getTotalProductPrice() {
		$this->recalcTotalPrices();
		return $this->totalProductPrice;
	}

	public function recalcTotalPrices() {
		$totalPrice = 0;
		$totalProductPrice = 0;
		foreach ($this->items as $item) {
			/* @var $item \SS6\ShopBundle\Model\Order\Item\OrderItem */
			$totalPrice += $item->getTotalPrice();

			if ($item instanceof OrderProduct) {
				$totalProductPrice += $item->getTotalPrice();
			}
		}
		$this->totalPrice = $totalPrice;
		$this->totalProductPrice = $totalProductPrice;
	}

	/**
	 * @return boolean
	 */
	public function isDeleted() {
		return $this->deleted;
	}

	/**
	 * @param boolean $deleted
	 */
	public function markAsDeleted() {
		$this->deleted = true;
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
	public function getNumber() {
		return $this->number;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	public function getCustomer() {
		return $this->customer;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreatedAt() {
		return $this->createdAt;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Order\Item\OrderItem[]
	 */
	public function getItems() {
		return $this->items;
	}

	/**
	 *
	 * @param int $orderItemId
	 * @return \SS6\ShopBundle\Model\Order\Item\OrderItem
	 * @throws \SS6\ShopBundle\Model\Order\Item\Exception\OrdetItemNotFoundException
	 */
	public function getItemById($orderItemId) {
		foreach ($this->getItems() as $orderItem) {
			if ($orderItem->getId() === $orderItemId) {
				return $orderItem;
			}
		}
		throw new \SS6\ShopBundle\Model\Order\Item\Exception\OrdetItemNotFoundException(array('id' => $orderItemId));
	}

	/**
	 * @return string
	 */
	public function getFirstName() {
		return $this->firstName;
	}

	/**
	 * @return string
	 */
	public function getLastName() {
		return $this->lastName;
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @return string
	 */
	public function getTelephone() {
		return $this->telephone;
	}

	/**
	 * @return string
	 */
	public function getCompanyName() {
		return $this->companyName;
	}

	/**
	 * @return string
	 */
	public function getCompanyNumber() {
		return $this->companyNumber;
	}

	/**
	 * @return string
	 */
	public function getCompanyTaxNumber() {
		return $this->companyTaxNumber;
	}

	/**
	 * @return string
	 */
	public function getStreet() {
		return $this->street;
	}

	/**
	 * @return string
	 */
	public function getCity() {
		return $this->city;
	}

	/**
	 * @return string
	 */
	public function getPostcode() {
		return $this->postcode;
	}

	/**
	 * @return string
	 */
	public function getDeliveryFirstName() {
		return $this->deliveryFirstName;
	}

	/**
	 * @return string
	 */
	public function getDeliveryLastName() {
		return $this->deliveryLastName;
	}

	/**
	 * @return string
	 */
	public function getDeliveryCompanyName() {
		return $this->deliveryCompanyName;
	}

	/**
	 * @return string
	 */
	public function getDeliveryTelephone() {
		return $this->deliveryTelephone;
	}

	/**
	 * @return string
	 */
	public function getDeliveryStreet() {
		return $this->deliveryStreet;
	}

	/**
	 * @return string
	 */
	public function getDeliveryCity() {
		return $this->deliveryCity;
	}

	/**
	 * @return string
	 */
	public function getDeliveryPostcode() {
		return $this->deliveryPostcode;
	}

	/**
	 * @return string
	 */
	public function getNote() {
		return $this->note;
	}

}
