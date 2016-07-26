<?php

namespace SS6\ShopBundle\Model\Order;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Order\Item\OrderItem;
use SS6\ShopBundle\Model\Order\Item\OrderPayment;
use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Order\Item\OrderTransport;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;

/**
 * @ORM\Table(name="orders")
 * @ORM\Entity
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Order {

	/**
	 * @var int
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
	 * @var \SS6\ShopBundle\Model\Customer\User|null
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Customer\User")
	 * @ORM\JoinColumn(nullable=true, name="customer_id", referencedColumnName="id", onDelete="SET NULL")
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
	 * @ORM\OrderBy({"id" = "ASC"})
	 */
	private $items;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Transport\Transport")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $transport;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Payment\Payment")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $payment;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Status\OrderStatus
	 *
	 * @ORM\ManyToOne(targetEntity="\SS6\ShopBundle\Model\Order\Status\OrderStatus")
	 * @ORM\JoinColumn(nullable=false)
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
	private $totalProductPriceWithVat;

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
	 * @var \SS6\ShopBundle\Model\Country\Country
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Country\Country")
	 * @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=false)
	 */
	private $country;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $deliveryAddressSameAsBillingAddress;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=200)
	 */
	private $deliveryContactPerson;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $deliveryCompanyName;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=30, nullable=true)
	 */
	private $deliveryTelephone;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=100)
	 */
	private $deliveryStreet;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100)
	 */
	private $deliveryCity;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=30)
	 */
	private $deliveryPostcode;

	/**
	 * @var \SS6\ShopBundle\Model\Country\Country|null
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Country\Country")
	 * @ORM\JoinColumn(name="delivery_country_id", referencedColumnName="id", nullable=true)
	 */
	private $deliveryCountry;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $note;

	/**
	 * @var int
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
	 * @var string
	 *
	 * @ORM\Column(type="string", length=50, unique=true)
	 */
	private $urlHash;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\Currency
	 *
	 * @ORM\ManyToOne(targetEntity="\SS6\ShopBundle\Model\Pricing\Currency\Currency")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $currency;

	/**
	 * @var \SS6\ShopBundle\Model\Administrator\Administrator|null
	 *
	 * @ORM\ManyToOne(targetEntity="\SS6\ShopBundle\Model\Administrator\Administrator")
	 * @ORM\JoinColumn(nullable=true, name="administrator_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	private $createdAsAdministrator;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $createdAsAdministratorName;

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param string $orderNumber
	 * @param string $urlHash
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 */
	public function __construct(
		OrderData $orderData,
		$orderNumber,
		$urlHash,
		User $user = null
	) {
		$this->transport = $orderData->transport;
		$this->payment = $orderData->payment;
		$this->firstName = $orderData->firstName;
		$this->lastName = $orderData->lastName;
		$this->email = $orderData->email;
		$this->telephone = $orderData->telephone;
		$this->street = $orderData->street;
		$this->city = $orderData->city;
		$this->postcode = $orderData->postcode;
		$this->country = $orderData->country;
		$this->note = $orderData->note;
		$this->items = new ArrayCollection();
		$this->setCompanyInfo(
			$orderData->companyName,
			$orderData->companyNumber,
			$orderData->companyTaxNumber
		);
		$this->setDeliveryAddress($orderData);
		$this->number = $orderNumber;
		$this->status = $orderData->status;
		$this->customer = $user;
		$this->deleted = false;
		if ($orderData->createdAt === null) {
			$this->createdAt = new DateTime();
		} else {
			$this->createdAt = $orderData->createdAt;
		}
		$this->domainId = $orderData->domainId;
		$this->urlHash = $urlHash;
		$this->currency = $orderData->currency;
		$this->createdAsAdministrator = $orderData->createdAsAdministrator;
		$this->createdAsAdministratorName = $orderData->createdAsAdministratorName;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 */
	public function edit(OrderData $orderData) {
		$this->firstName = $orderData->firstName;
		$this->lastName = $orderData->lastName;
		$this->email = $orderData->email;
		$this->telephone = $orderData->telephone;
		$this->street = $orderData->street;
		$this->city = $orderData->city;
		$this->postcode = $orderData->postcode;
		$this->country = $orderData->country;
		$this->note = $orderData->note;

		$this->setCompanyInfo(
			$orderData->companyName,
			$orderData->companyNumber,
			$orderData->companyTaxNumber
		);
		$this->setDeliveryAddress($orderData);
		$this->status = $orderData->status;

		$this->editOrderTransport($orderData);
		$this->editOrderPayment($orderData);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 */
	private function editOrderTransport(OrderData $orderData) {
		$orderTransportData = $orderData->orderTransport;
		$this->transport = $orderTransportData->transport;
		$this->getOrderTransport()->edit($orderTransportData);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 */
	private function editOrderPayment(OrderData $orderData) {
		$orderPaymentData = $orderData->orderPayment;
		$this->payment = $orderPaymentData->payment;
		$this->getOrderPayment()->edit($orderPaymentData);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 */
	private function setDeliveryAddress(OrderData $orderData) {
		$this->deliveryAddressSameAsBillingAddress = $orderData->deliveryAddressSameAsBillingAddress;
		if ($orderData->deliveryAddressSameAsBillingAddress) {
			$this->deliveryContactPerson = $orderData->firstName . ' ' . $orderData->lastName;
			$this->deliveryCompanyName = $orderData->companyName;
			$this->deliveryTelephone = $orderData->telephone;
			$this->deliveryStreet = $orderData->street;
			$this->deliveryCity = $orderData->city;
			$this->deliveryPostcode = $orderData->postcode;
			$this->deliveryCountry = $orderData->country;
		} else {
			$this->deliveryContactPerson = $orderData->deliveryContactPerson;
			$this->deliveryCompanyName = $orderData->deliveryCompanyName;
			$this->deliveryTelephone = $orderData->deliveryTelephone;
			$this->deliveryStreet = $orderData->deliveryStreet;
			$this->deliveryCity = $orderData->deliveryCity;
			$this->deliveryPostcode = $orderData->deliveryPostcode;
			$this->deliveryCountry = $orderData->deliveryCountry;
		}
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
		return $this->getOrderPayment()->getName();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Order\Item\OrderPayment
	 */
	public function getOrderPayment() {
		foreach ($this->items as $item) {
			if ($item instanceof OrderPayment) {
				return $item;
			}
		}
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
		return $this->getOrderTransport()->getName();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Order\Item\OrderTransport
	 */
	public function getOrderTransport() {
		foreach ($this->items as $item) {
			if ($item instanceof OrderTransport) {
				return $item;
			}
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\Order\Status\OrderStatus
	 */
	public function getStatus() {
		return $this->status;
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
	public function getTotalProductPriceWithVat() {
		return $this->totalProductPriceWithVat;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Currency\Currency
	 */
	public function getCurrency() {
		return $this->currency;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderTotalPrice $orderTotalPrice
	 */
	public function setTotalPrice(OrderTotalPrice $orderTotalPrice) {
		$this->totalPriceWithVat = $orderTotalPrice->getPriceWithVat();
		$this->totalPriceWithoutVat = $orderTotalPrice->getPriceWithoutVat();
		$this->totalProductPriceWithVat = $orderTotalPrice->getProductPriceWithVat();
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
	 * @return \SS6\ShopBundle\Model\Customer\User|null
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
	 * @return \SS6\ShopBundle\Model\Order\Item\OrderItem[]
	 */
	public function getItemsWithoutTransportAndPayment() {
		$itemsWithoutTransportAndPayment = [];
		foreach ($this->getItems() as $orderItem) {
			if (!($orderItem instanceof OrderTransport || $orderItem instanceof OrderPayment)) {
				$itemsWithoutTransportAndPayment[] = $orderItem;
			}
		}

		return $itemsWithoutTransportAndPayment;
	}

	/**
	 * @param int $orderItemId
	 * @return \SS6\ShopBundle\Model\Order\Item\OrderItem
	 */
	public function getItemById($orderItemId) {
		foreach ($this->getItems() as $orderItem) {
			if ($orderItem->getId() === $orderItemId) {
				return $orderItem;
			}
		}
		throw new \SS6\ShopBundle\Model\Order\Item\Exception\OrdetItemNotFoundException(['id' => $orderItemId]);
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
	 * @return \SS6\ShopBundle\Model\Country\Country
	 */
	public function getCountry() {
		return $this->country;
	}

	/**
	 * @return bool
	 */
	public function isDeliveryAddressSameAsBillingAddress() {
		return $this->deliveryAddressSameAsBillingAddress;
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
	 * @return \SS6\ShopBundle\Model\Country\Country|null
	 */
	public function getDeliveryCountry() {
		return $this->deliveryCountry;
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

	/**
	 * @return string
	 */
	public function getUrlHash() {
		return $this->urlHash;
	}

	/**
	 * @return int
	 */
	public function getProductItemsCount() {
		return count($this->getProductItems());
	}

	/**
	 * @return \SS6\ShopBundle\Model\Order\Item\OrderProduct[]
	 */
	public function getProductItems() {
		$productItems = [];
		foreach ($this->items as $item) {
			if ($item instanceof OrderProduct) {
				$productItems[] = $item;
			}
		}

		return $productItems;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Administrator\Administrator|null
	 */
	public function getCreatedAsAdministrator() {
		return $this->createdAsAdministrator;
	}

	/**
	 * @return string|null
	 */
	public function getCreatedAsAdministratorName() {
		return $this->createdAsAdministratorName;
	}

}
