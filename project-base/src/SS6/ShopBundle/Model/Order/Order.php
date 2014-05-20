<?php

namespace SS6\ShopBundle\Model\Order;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Order\Item\OrderItemAbstract;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Transport\Transport;

/**
 * @ORM\Table(name="orders")
 * @ORM\Entity
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
	private $createdOn;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\OrderItemAbstract
	 *
	 * @ORM\OneToMany(targetEntity="SS6\ShopBundle\Model\Order\Item\OrderItemAbstract", mappedBy="order", orphanRemoval=true)
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
	private $zip;

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
	private $deliveryZip;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $note;

	/**
	 *
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $email
	 * @param string $telephone
	 * @param string $street
	 * @param string $city
	 * @param string $zip
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
	 * @param string|null $deliveryZip
	 * @param string|null $note
	 */
	public function __construct($number, Transport $transport, Payment $payment, $firstName, $lastName, $email,
			$telephone, $street, $city, $zip, User $user = null, $companyName = null,
			$companyNumber = null, $companyTaxNumber = null, $deliveryFirstName = null, $deliveryLastName = null,
			$deliveryCompanyName = null, $deliveryTelephone = null, $deliveryStreet = null, $deliveryCity = null,
			$deliveryZip = null, $note = null) {
		$this->number = $number;
		$this->customer = $user;
		$this->items = new ArrayCollection();
		$this->createdOn = new DateTime();
		$this->transport = $transport;
		$this->payment = $payment;
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->email = $email;
		$this->telephone = $telephone;
		$this->companyName = $companyName;
		$this->companyNumber = $companyNumber;
		$this->companyTaxNumber = $companyTaxNumber;
		$this->street = $street;
		$this->city = $city;
		$this->zip = $zip;
		$this->deliveryFirstName = $deliveryFirstName;
		$this->deliveryLastName = $deliveryLastName;
		$this->deliveryCompanyName = $deliveryCompanyName;
		$this->deliveryTelephone = $deliveryTelephone;
		$this->deliveryStreet = $deliveryStreet;
		$this->deliveryCity = $deliveryCity;
		$this->deliveryZip = $deliveryZip;
		$this->note = $note;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItemAbstract $item
	 */
	public function addItem(OrderItemAbstract $item) {
		if (!$this->items->contains($item)) {
			$this->items->add($item);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItemAbstract $item
	 */
	public function removeItem(OrderItemAbstract $item) {
		$this->items->removeElement($item);
	}

	/**
	 * @return SS6\ShopBundle\Model\Payment\Payment
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

	public function detachCustomer() {
		$this->customer = null;
	}
}
