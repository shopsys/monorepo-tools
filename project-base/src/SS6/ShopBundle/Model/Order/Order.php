<?php

namespace SS6\ShopBundle\Model\Order;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Order\Item\OrderItem;
use SS6\ShopBundle\Model\Order\Item\OrderPayment;
use SS6\ShopBundle\Model\Order\Item\OrderTransport;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;

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
	private $totalPriceWithVat;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="decimal", precision=20, scale=6)
	 */
	private $totalPriceWithoutVat;

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
	 * @ORM\Column(type="string", length=200, nullable=true)
	 */
	private $deliveryContactPerson;

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
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 */
	private $domainId;

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param string $orderNumber
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 */
	public function __construct(
		OrderData $orderData,
		$orderNumber,
		OrderStatus $orderStatus,
		User $user = null
	) {
		$this->transport = $orderData->getTransport();
		$this->payment = $orderData->getPayment();
		$this->firstName = $orderData->getFirstName();
		$this->lastName = $orderData->getLastName();
		$this->email = $orderData->getEmail();
		$this->telephone = $orderData->getTelephone();
		$this->street = $orderData->getStreet();
		$this->city = $orderData->getCity();
		$this->postcode = $orderData->getPostcode();
		$this->note = $orderData->getNote();
		$this->items = new ArrayCollection();
		if ($orderData->isCompanyCustomer()) {
			$this->setCompanyInfo(
				$orderData->getCompanyName(),
				$orderData->getCompanyNumber(),
				$orderData->getCompanyTaxNumber()
			);
		}
		if ($orderData->isDeliveryAddressFilled()) {
			$this->setDeliveryAddress(
				$orderData->getDeliveryContactPerson(),
				$orderData->getDeliveryCompanyName(),
				$orderData->getDeliveryTelephone(),
				$orderData->getDeliveryStreet(),
				$orderData->getDeliveryCity(),
				$orderData->getDeliveryPostcode());
		}
		$this->number = $orderNumber;
		$this->status = $orderStatus;
		$this->customer = $user;
		$this->deleted = false;
		$this->createdAt = new DateTime();
		$this->domainId = $orderData->getDomainId();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 */
	public function edit(
		OrderData $orderData,
		OrderStatus $orderStatus,
		User $user = null
	) {
		$this->firstName = $orderData->getFirstName();
		$this->lastName = $orderData->getLastName();
		$this->email = $orderData->getEmail();
		$this->telephone = $orderData->getTelephone();
		$this->street = $orderData->getStreet();
		$this->city = $orderData->getCity();
		$this->postcode = $orderData->getPostcode();
		$this->note = $orderData->getNote();

		if ($orderData->isCompanyCustomer()) {
			$this->setCompanyInfo(
				$orderData->getCompanyName(),
				$orderData->getCompanyNumber(),
				$orderData->getCompanyTaxNumber()
			);
		}
		if ($orderData->isDeliveryAddressFilled()) {
			$this->setDeliveryAddress(
				$orderData->getDeliveryContactPerson(),
				$orderData->getDeliveryCompanyName(),
				$orderData->getDeliveryTelephone(),
				$orderData->getDeliveryStreet(),
				$orderData->getDeliveryCity(),
				$orderData->getDeliveryPostcode());
		}
		$this->status = $orderStatus;
		$this->customer = $user;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItem $item
	 */
	public function addItem(OrderItem $item) {
		if (!$this->items->contains($item)) {
			$this->items->add($item);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItem $item
	 */
	public function removeItem(OrderItem $item) {
		if ($item instanceof OrderTransport) {
			$this->transport = null;
		}
		if ($item instanceof OrderPayment) {
			$this->payment = null;
		}
		$this->items->removeElement($item);
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
	 * @param string|null $deliveryContactPerson
	 * @param string|null $deliveryCompanyName
	 * @param string|null $deliveryTelephone
	 * @param string|null $deliveryStreet
	 * @param string|null $deliveryCity
	 * @param string|null $deliveryPostcode
	 */
	public function setDeliveryAddress(
		$deliveryContactPerson = null,
		$deliveryCompanyName = null,
		$deliveryTelephone = null,
		$deliveryStreet = null,
		$deliveryCity = null,
		$deliveryPostcode = null
	) {
		$this->deliveryContactPerson = $deliveryContactPerson;
		$this->deliveryCompanyName = $deliveryCompanyName;
		$this->deliveryTelephone = $deliveryTelephone;
		$this->deliveryStreet = $deliveryStreet;
		$this->deliveryCity = $deliveryCity;
		$this->deliveryPostcode = $deliveryPostcode;
	}

	/**
	 * @param int $domainId
	 */
	public function setDomainId($domainId) {
		$this->domainId = $domainId;
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
	public function getPaymentName() {
		foreach ($this->items as $item) {
			if ($item instanceof OrderPayment) {
				return $item->getName();
			}
		}

		return null;
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
	public function getTransportName() {
		foreach ($this->items as $item) {
			if ($item instanceof OrderTransport) {
				return $item->getName();
			}
		}

		return null;
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
	public function getTotalPriceWithVat() {
		return $this->totalPriceWithVat;
	}

	/**
	 * @return string
	 */
	public function getTotalPriceWithoutVat() {
		return $this->totalPriceWithoutVat;
	}

	/**
	 * @return string
	 */
	public function getTotalProductPrice() {
		return $this->totalProductPrice;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderTotalPrice $orderTotalPrice
	 */
	public function setTotalPrice(OrderTotalPrice $orderTotalPrice) {
		$this->totalPriceWithVat = $orderTotalPrice->getPriceWithVat();
		$this->totalPriceWithoutVat = $orderTotalPrice->getPriceWithoutVat();
		$this->totalProductPrice = $orderTotalPrice->getProductPrice();
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
	public function getDeliveryContactPerson() {
		return $this->deliveryContactPerson;
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

	/**
	 * @return int
	 */
	public function getDomainId() {
		return $this->domainId;
	}

}
