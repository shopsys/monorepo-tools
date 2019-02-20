<?php

namespace Shopsys\FrameworkBundle\Model\Order;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItem;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemFactoryInterface;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation;
use Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreview;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus;
use Shopsys\FrameworkBundle\Model\Payment\PaymentPriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation;
use Shopsys\FrameworkBundle\Twig\NumberFormatterExtension;

/**
 * @ORM\Table(name="orders")
 * @ORM\Entity
 */
class Order
{
    const DEFAULT_PRODUCT_QUANTITY = 1;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=30, unique=true, nullable=false)
     */
    protected $number;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\User|null
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Customer\User")
     * @ORM\JoinColumn(nullable=true, name="customer_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $customer;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Shopsys\FrameworkBundle\Model\Order\Item\OrderItem",
     *     mappedBy="order",
     *     cascade={"persist"},
     *     orphanRemoval=true
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $items;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\Transport
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Transport\Transport")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $transport;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\Payment
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Payment\Payment")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $payment;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $status;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money
     *
     * @ORM\Column(type="money", precision=20, scale=6)
     */
    protected $totalPriceWithVat;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money
     *
     * @ORM\Column(type="money", precision=20, scale=6)
     */
    protected $totalPriceWithoutVat;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money
     *
     * @ORM\Column(type="money", precision=20, scale=6)
     */
    protected $totalProductPriceWithVat;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=30)
     */
    protected $telephone;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $companyName;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $companyNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $companyTaxNumber;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    protected $street;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    protected $city;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=30)
     */
    protected $postcode;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Country\Country
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Country\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=false)
     */
    protected $country;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $deliveryAddressSameAsBillingAddress;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    protected $deliveryFirstName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    protected $deliveryLastName;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $deliveryCompanyName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    protected $deliveryTelephone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    protected $deliveryStreet;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=100)
     */
    protected $deliveryCity;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=30)
     */
    protected $deliveryPostcode;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Country\Country|null
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Country\Country")
     * @ORM\JoinColumn(name="delivery_country_id", referencedColumnName="id", nullable=true)
     */
    protected $deliveryCountry;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $note;

    /**
     * @var int
     *
     * @ORM\Column(type="boolean")
     */
    protected $deleted;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $domainId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, unique=true)
     */
    protected $urlHash;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $currency;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Administrator\Administrator|null
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Administrator\Administrator")
     * @ORM\JoinColumn(nullable=true, name="administrator_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $createdAsAdministrator;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $createdAsAdministratorName;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderData $orderData
     * @param string $orderNumber
     * @param string $urlHash
     * @param \Shopsys\FrameworkBundle\Model\Customer\User|null $user
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
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderData $orderData
     */
    protected function editData(OrderData $orderData)
    {
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
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderData $orderData
     */
    protected function editOrderTransport(OrderData $orderData)
    {
        $orderTransportData = $orderData->orderTransport;
        $this->transport = $orderTransportData->transport;
        $this->getOrderTransport()->edit($orderTransportData);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderData $orderData
     */
    protected function editOrderPayment(OrderData $orderData)
    {
        $orderPaymentData = $orderData->orderPayment;
        $this->payment = $orderPaymentData->payment;
        $this->getOrderPayment()->edit($orderPaymentData);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderData $orderData
     */
    protected function setDeliveryAddress(OrderData $orderData)
    {
        $this->deliveryAddressSameAsBillingAddress = $orderData->deliveryAddressSameAsBillingAddress;
        if ($orderData->deliveryAddressSameAsBillingAddress) {
            $this->deliveryFirstName = $orderData->firstName;
            $this->deliveryLastName = $orderData->lastName;
            $this->deliveryCompanyName = $orderData->companyName;
            $this->deliveryTelephone = $orderData->telephone;
            $this->deliveryStreet = $orderData->street;
            $this->deliveryCity = $orderData->city;
            $this->deliveryPostcode = $orderData->postcode;
            $this->deliveryCountry = $orderData->country;
        } else {
            $this->deliveryFirstName = $orderData->deliveryFirstName;
            $this->deliveryLastName = $orderData->deliveryLastName;
            $this->deliveryCompanyName = $orderData->deliveryCompanyName;
            $this->deliveryTelephone = $orderData->deliveryTelephone;
            $this->deliveryStreet = $orderData->deliveryStreet;
            $this->deliveryCity = $orderData->deliveryCity;
            $this->deliveryPostcode = $orderData->deliveryPostcode;
            $this->deliveryCountry = $orderData->deliveryCountry;
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem $item
     */
    public function addItem(OrderItem $item)
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem $item
     */
    public function removeItem(OrderItem $item)
    {
        if ($item->isTypeTransport()) {
            $this->transport = null;
        }
        if ($item->isTypePayment()) {
            $this->payment = null;
        }
        $this->items->removeElement($item);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus $status
     */
    public function setStatus(OrderStatus $status)
    {
        $this->status = $status;
    }

    /**
     * @param string|null $companyName
     * @param string|null $companyNumber
     * @param string|null $companyTaxNumber
     */
    public function setCompanyInfo($companyName = null, $companyNumber = null, $companyTaxNumber = null)
    {
        $this->companyName = $companyName;
        $this->companyNumber = $companyNumber;
        $this->companyTaxNumber = $companyTaxNumber;
    }

    /**
     * @param int $domainId
     */
    public function setDomainId($domainId)
    {
        $this->domainId = $domainId;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @return string
     */
    public function getPaymentName()
    {
        return $this->getOrderPayment()->getName();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem
     */
    public function getOrderPayment()
    {
        foreach ($this->items as $item) {
            if ($item->isTypePayment()) {
                return $item;
            }
        }
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Transport\Transport
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @return string
     */
    public function getTransportName()
    {
        return $this->getOrderTransport()->getName();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem
     */
    public function getOrderTransport()
    {
        foreach ($this->items as $item) {
            if ($item->isTypeTransport()) {
                return $item;
            }
        }
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getTotalPriceWithVat()
    {
        return $this->totalPriceWithVat->toValue();
    }

    /**
     * @return string
     */
    public function getTotalPriceWithoutVat()
    {
        return $this->totalPriceWithoutVat->toValue();
    }

    /**
     * @return string
     */
    public function getTotalVatAmount()
    {
        return $this->totalPriceWithVat->subtract($this->totalPriceWithoutVat)->toValue();
    }

    /**
     * @return string
     */
    public function getTotalProductPriceWithVat()
    {
        return $this->totalProductPriceWithVat->toValue();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderTotalPrice $orderTotalPrice
     */
    protected function setTotalPrice(OrderTotalPrice $orderTotalPrice)
    {
        $this->totalPriceWithVat = Money::fromValue($orderTotalPrice->getPriceWithVat());
        $this->totalPriceWithoutVat = Money::fromValue($orderTotalPrice->getPriceWithoutVat());
        $this->totalProductPriceWithVat = Money::fromValue($orderTotalPrice->getProductPriceWithVat());
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    public function markAsDeleted()
    {
        $this->deleted = true;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Customer\User|null
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem[]
     */
    public function getItemsWithoutTransportAndPayment()
    {
        $itemsWithoutTransportAndPayment = [];
        foreach ($this->getItems() as $orderItem) {
            if (!($orderItem->isTypeTransport() || $orderItem->isTypePayment())) {
                $itemsWithoutTransportAndPayment[] = $orderItem;
            }
        }

        return $itemsWithoutTransportAndPayment;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem[]
     */
    protected function getTransportAndPaymentItems()
    {
        $transportAndPaymentItems = [];
        foreach ($this->getItems() as $orderItem) {
            if ($orderItem->isTypeTransport() || $orderItem->isTypePayment()) {
                $transportAndPaymentItems[] = $orderItem;
            }
        }

        return $transportAndPaymentItems;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function getTransportAndPaymentPrice()
    {
        $transportAndPaymentItems = $this->getTransportAndPaymentItems();
        $totalPrice = new Price(0, 0);

        foreach ($transportAndPaymentItems as $item) {
            $itemPrice = new Price($item->getPriceWithoutVat(), $item->getPriceWithVat());
            $totalPrice = $totalPrice->add($itemPrice);
        }

        return $totalPrice;
    }

    /**
     * @param int $orderItemId
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem
     */
    public function getItemById($orderItemId)
    {
        foreach ($this->getItems() as $orderItem) {
            if ($orderItem->getId() === $orderItemId) {
                return $orderItem;
            }
        }
        throw new \Shopsys\FrameworkBundle\Model\Order\Item\Exception\OrderItemNotFoundException(['id' => $orderItemId]);
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @return string
     */
    public function getCompanyNumber()
    {
        return $this->companyNumber;
    }

    /**
     * @return string
     */
    public function getCompanyTaxNumber()
    {
        return $this->companyTaxNumber;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Country\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return bool
     */
    public function isDeliveryAddressSameAsBillingAddress()
    {
        return $this->deliveryAddressSameAsBillingAddress;
    }

    /**
     * @return string
     */
    public function getDeliveryFirstName()
    {
        return $this->deliveryFirstName;
    }

    /**
     * @return string
     */
    public function getDeliveryLastName()
    {
        return $this->deliveryLastName;
    }

    /**
     * @return string
     */
    public function getDeliveryCompanyName()
    {
        return $this->deliveryCompanyName;
    }

    /**
     * @return string
     */
    public function getDeliveryTelephone()
    {
        return $this->deliveryTelephone;
    }

    /**
     * @return string
     */
    public function getDeliveryStreet()
    {
        return $this->deliveryStreet;
    }

    /**
     * @return string
     */
    public function getDeliveryCity()
    {
        return $this->deliveryCity;
    }

    /**
     * @return string
     */
    public function getDeliveryPostcode()
    {
        return $this->deliveryPostcode;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Country\Country|null
     */
    public function getDeliveryCountry()
    {
        return $this->deliveryCountry;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @return int
     */
    public function getDomainId()
    {
        return $this->domainId;
    }

    /**
     * @return string
     */
    public function getUrlHash()
    {
        return $this->urlHash;
    }

    /**
     * @return int
     */
    public function getProductItemsCount()
    {
        return count($this->getProductItems());
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem[]
     */
    public function getProductItems()
    {
        $productItems = [];
        foreach ($this->items as $item) {
            if ($item->isTypeProduct()) {
                $productItems[] = $item;
            }
        }

        return $productItems;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Administrator\Administrator|null
     */
    public function getCreatedAsAdministrator()
    {
        return $this->createdAsAdministrator;
    }

    /**
     * @return string|null
     */
    public function getCreatedAsAdministratorName()
    {
        return $this->createdAsAdministratorName;
    }

    /**
     * @return bool
     */
    public function isCancelled()
    {
        return $this->status === OrderStatus::TYPE_CANCELED;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderPriceCalculation $orderPriceCalculation
     */
    public function calculateTotalPrice(OrderPriceCalculation $orderPriceCalculation)
    {
        $orderTotalPrice = $orderPriceCalculation->getOrderTotalPrice($this);
        $this->setTotalPrice($orderTotalPrice);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $productPrice
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemFactoryInterface $orderItemFactory
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderPriceCalculation $orderPriceCalculation
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem
     */
    public function addProduct(
        Product $product,
        Price $productPrice,
        OrderItemFactoryInterface $orderItemFactory,
        Domain $domain,
        OrderPriceCalculation $orderPriceCalculation
    ): OrderItem {
        $orderDomainConfig = $domain->getDomainConfigById($this->getDomainId());

        $orderProduct = $orderItemFactory->createProduct(
            $this,
            $product->getName($orderDomainConfig->getLocale()),
            $productPrice,
            $product->getVat()->getPercent(),
            self::DEFAULT_PRODUCT_QUANTITY,
            $product->getUnit()->getName($orderDomainConfig->getLocale()),
            $product->getCatnum(),
            $product
        );

        $this->addItem($orderProduct);
        $this->calculateTotalPrice($orderPriceCalculation);

        return $orderProduct;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderData $orderData
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation $orderItemPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemFactoryInterface $orderItemFactory
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderPriceCalculation $orderPriceCalculation
     * @return \Shopsys\FrameworkBundle\Model\Order\OrderEditResult
     */
    public function edit(
        OrderData $orderData,
        OrderItemPriceCalculation $orderItemPriceCalculation,
        OrderItemFactoryInterface $orderItemFactory,
        OrderPriceCalculation $orderPriceCalculation
    ): OrderEditResult {
        $orderTransportData = $orderData->orderTransport;
        $orderTransportData->priceWithoutVat = Money::fromValue($orderItemPriceCalculation->calculatePriceWithoutVat($orderTransportData));
        $orderPaymentData = $orderData->orderPayment;
        $orderPaymentData->priceWithoutVat = Money::fromValue($orderItemPriceCalculation->calculatePriceWithoutVat($orderPaymentData));

        $statusChanged = $this->getStatus() !== $orderData->status;
        $this->editData($orderData);

        $orderItemsWithoutTransportAndPaymentData = $orderData->itemsWithoutTransportAndPayment;

        foreach ($this->getItemsWithoutTransportAndPayment() as $orderItem) {
            if (array_key_exists($orderItem->getId(), $orderItemsWithoutTransportAndPaymentData)) {
                $orderItemData = $orderItemsWithoutTransportAndPaymentData[$orderItem->getId()];
                $orderItemData->priceWithoutVat = Money::fromValue($orderItemPriceCalculation->calculatePriceWithoutVat($orderItemData));
                $orderItem->edit($orderItemData);
            } else {
                $this->removeItem($orderItem);
            }
        }

        foreach ($orderData->getNewItemsWithoutTransportAndPayment() as $newOrderItemData) {
            $newOrderItemData->priceWithoutVat = Money::fromValue($orderItemPriceCalculation->calculatePriceWithoutVat($newOrderItemData));
            $orderItemFactory->createProduct(
                $this,
                $newOrderItemData->name,
                new Price(
                    $newOrderItemData->priceWithoutVat->toValue(),
                    $newOrderItemData->priceWithVat->toValue()
                ),
                $newOrderItemData->vatPercent,
                $newOrderItemData->quantity,
                $newOrderItemData->unitName,
                $newOrderItemData->catnum
            );
        }

        $this->calculateTotalPrice($orderPriceCalculation);

        return new OrderEditResult($statusChanged);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentPriceCalculation $paymentPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemFactoryInterface $orderItemFactory
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $productsPrice
     * @param string $locale
     */
    public function fillOrderPayment(
        PaymentPriceCalculation $paymentPriceCalculation,
        OrderItemFactoryInterface $orderItemFactory,
        Price $productsPrice,
        $locale
    ) {
        $payment = $this->getPayment();
        $paymentPrice = $paymentPriceCalculation->calculatePrice(
            $payment,
            $this->getCurrency(),
            $productsPrice,
            $this->getDomainId()
        );
        $orderPayment = $orderItemFactory->createPayment(
            $this,
            $payment->getName($locale),
            $paymentPrice,
            $payment->getVat()->getPercent(),
            1,
            $payment
        );
        $this->addItem($orderPayment);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation $transportPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemFactoryInterface $orderItemFactory
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $productsPrice
     * @param string $locale
     */
    public function fillOrderTransport(
        TransportPriceCalculation $transportPriceCalculation,
        OrderItemFactoryInterface $orderItemFactory,
        Price $productsPrice,
        $locale
    ) {
        $transport = $this->getTransport();
        $transportPrice = $transportPriceCalculation->calculatePrice(
            $transport,
            $this->getCurrency(),
            $productsPrice,
            $this->getDomainId()
        );
        $orderTransport = $orderItemFactory->createTransport(
            $this,
            $transport->getName($locale),
            $transportPrice,
            $transport->getVat()->getPercent(),
            1,
            $transport
        );
        $this->addItem($orderTransport);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreview $orderPreview
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemFactoryInterface $orderItemFactory
     * @param \Shopsys\FrameworkBundle\Twig\NumberFormatterExtension $numberFormatterExtension
     * @param string $locale
     */
    public function fillOrderProducts(
        OrderPreview $orderPreview,
        OrderItemFactoryInterface $orderItemFactory,
        NumberFormatterExtension $numberFormatterExtension,
        $locale
    ) {
        $quantifiedItemPrices = $orderPreview->getQuantifiedItemsPrices();
        $quantifiedItemDiscounts = $orderPreview->getQuantifiedItemsDiscounts();

        foreach ($orderPreview->getQuantifiedProducts() as $index => $quantifiedProduct) {
            $product = $quantifiedProduct->getProduct();
            if (!$product instanceof Product) {
                $message = 'Object "' . get_class($product) . '" is not valid for order creation.';
                throw new \Shopsys\FrameworkBundle\Model\Order\Item\Exception\InvalidQuantifiedProductException($message);
            }

            $quantifiedItemPrice = $quantifiedItemPrices[$index];
            /* @var $quantifiedItemPrice \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedItemPrice */
            $quantifiedItemDiscount = $quantifiedItemDiscounts[$index];
            /* @var $quantifiedItemDiscount \Shopsys\FrameworkBundle\Model\Pricing\Price|null */

            $orderItem = $orderItemFactory->createProduct(
                $this,
                $product->getName($locale),
                $quantifiedItemPrice->getUnitPrice(),
                $product->getVat()->getPercent(),
                $quantifiedProduct->getQuantity(),
                $product->getUnit()->getName($locale),
                $product->getCatnum(),
                $product
            );

            if ($quantifiedItemDiscount !== null) {
                $this->addOrderItemDiscount($numberFormatterExtension, $orderPreview, $orderItemFactory, $quantifiedItemDiscount, $orderItem, $locale);
            }
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Twig\NumberFormatterExtension $numberFormatterExtension
     * @param \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreview $orderPreview
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemFactoryInterface $orderItemFactory
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $quantifiedItemDiscount
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem $orderItem
     * @param string $locale
     */
    protected function addOrderItemDiscount(
        NumberFormatterExtension $numberFormatterExtension,
        OrderPreview $orderPreview,
        OrderItemFactoryInterface $orderItemFactory,
        Price $quantifiedItemDiscount,
        OrderItem $orderItem,
        $locale
    ) {
        $name = sprintf(
            '%s %s - %s',
            t('Promo code', [], 'messages', $locale),
            $numberFormatterExtension->formatPercent(-$orderPreview->getPromoCodeDiscountPercent(), $locale),
            $orderItem->getName()
        );

        $orderItemFactory->createProduct(
            $orderItem->getOrder(),
            $name,
            new Price(
                -$quantifiedItemDiscount->getPriceWithoutVat(),
                -$quantifiedItemDiscount->getPriceWithVat()
            ),
            $orderItem->getVatPercent(),
            1,
            null,
            null,
            null
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemFactoryInterface $orderItemFactory
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price|null $roundingPrice
     * @param string $locale
     */
    public function fillOrderRounding(OrderItemFactoryInterface $orderItemFactory, ?Price $roundingPrice, $locale)
    {
        if ($roundingPrice !== null) {
            $orderItemFactory->createProduct(
                $this,
                t('Rounding', [], 'messages', $locale),
                $roundingPrice,
                0,
                1,
                null,
                null,
                null
            );
        }
    }
}
