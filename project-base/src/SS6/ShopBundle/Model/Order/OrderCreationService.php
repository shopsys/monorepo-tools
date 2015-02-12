<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation;
use SS6\ShopBundle\Model\Order\Item\OrderPayment;
use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Order\Item\OrderTransport;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Order\OrderPriceCalculation;
use SS6\ShopBundle\Model\Payment\PaymentPriceCalculation;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Transport\TransportPriceCalculation;

class OrderCreationService {

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation
	 */
	private $orderItemPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderPriceCalculation
	 */
	private $orderPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser
	 */
	private $productPriceCalculationForUser;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PaymentPriceCalculation
	 */
	private $paymentPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportPriceCalculation
	 */
	private $transportPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	public function __construct(
		OrderItemPriceCalculation $orderItemPriceCalculation,
		OrderPriceCalculation $orderPriceCalculation,
		ProductPriceCalculationForUser $productPriceCalculationForUser,
		PaymentPriceCalculation $paymentPriceCalculation,
		TransportPriceCalculation $transportPriceCalculation,
		Domain $domain
	) {
		$this->orderItemPriceCalculation = $orderItemPriceCalculation;
		$this->orderPriceCalculation = $orderPriceCalculation;
		$this->productPriceCalculationForUser = $productPriceCalculationForUser;
		$this->paymentPriceCalculation = $paymentPriceCalculation;
		$this->transportPriceCalculation = $transportPriceCalculation;
		$this->domain = $domain;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 */
	public function prefillFrontFormData(OrderData $orderData, User $user, Order $order = null) {
		if ($order instanceof Order) {
			$this->prefillTransportAndPaymentFromOrder($orderData, $order);
		}
		$this->prefillFrontFormDataFromCustomer($orderData, $user);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 */
	private function prefillTransportAndPaymentFromOrder(OrderData $orderData, Order $order) {
		$orderData->transport = $order->getTransport();
		$orderData->payment = $order->getPayment();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 */
	private function prefillFrontFormDataFromCustomer(OrderData $orderData, User $user) {
		$orderData->firstName = $user->getFirstName();
		$orderData->lastName = $user->getLastName();
		$orderData->email = $user->getEmail();
		$orderData->telephone = $user->getBillingAddress()->getTelephone();
		$orderData->companyCustomer = $user->getBillingAddress()->isCompanyCustomer();
		$orderData->companyName = $user->getBillingAddress()->getCompanyName();
		$orderData->companyNumber = $user->getBillingAddress()->getCompanyNumber();
		$orderData->companyTaxNumber = $user->getBillingAddress()->getCompanyTaxNumber();
		$orderData->street = $user->getBillingAddress()->getStreet();
		$orderData->city = $user->getBillingAddress()->getCity();
		$orderData->postcode = $user->getBillingAddress()->getPostcode();
		if ($user->getDeliveryAddress() !== null) {
			$orderData->deliveryAddressFilled = true;
			$orderData->deliveryContactPerson = $user->getDeliveryAddress()->getContactPerson();
			$orderData->deliveryCompanyName = $user->getDeliveryAddress()->getCompanyName();
			$orderData->deliveryTelephone = $user->getDeliveryAddress()->getTelephone();
			$orderData->deliveryStreet = $user->getDeliveryAddress()->getStreet();
			$orderData->deliveryCity = $user->getDeliveryAddress()->getCity();
			$orderData->deliveryPostcode = $user->getDeliveryAddress()->getPostcode();
		} else {
			$orderData->deliveryAddressFilled = false;
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Model\Order\Item\QuantifiedItem[] $quantifiedItems
	 */
	public function fillOrderItems(Order $order, array $quantifiedItems) {
		$locale = $this->domain->getDomainConfigById($order->getDomainId())->getLocale();

		$this->fillOrderProducts($order, $quantifiedItems, $locale);
		$this->fillOrderTransportAndPayment($order, $locale);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param string $locale
	 */
	private function fillOrderTransportAndPayment(Order $order, $locale) {
		$payment = $order->getPayment();
		$paymentPrice = $this->paymentPriceCalculation->calculatePrice($payment, $order->getCurrency());
		$orderPayment = new OrderPayment(
			$order,
			$payment->getName($locale),
			$paymentPrice->getPriceWithoutVat(),
			$paymentPrice->getPriceWithVat(),
			$payment->getVat()->getPercent(),
			1,
			$payment
		);
		$order->addItem($orderPayment);

		$transport = $order->getTransport();
		$transportPrice = $this->transportPriceCalculation->calculatePrice($transport, $order->getCurrency());
		$orderTransport = new OrderTransport(
			$order,
			$transport->getName($locale),
			$transportPrice->getPriceWithoutVat(),
			$transportPrice->getPriceWithVat(),
			$transport->getVat()->getPercent(),
			1,
			$transport
		);
		$order->addItem($orderTransport);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Model\Order\Item\QuantifiedItem[] $quantifiedItems
	 * @param string $locale
	 */
	private function fillOrderProducts(Order $order, array $quantifiedItems, $locale) {
		foreach ($quantifiedItems as $quantifiedItem) {
			$item = $quantifiedItem->getItem();
			if ($item instanceof Product) {
				$productPrice = $this->productPriceCalculationForUser->calculatePriceForUserAndDomainId(
					$item,
					$order->getDomainId(),
					$order->getCustomer()
				);

				$orderItem = new OrderProduct(
					$order,
					$item->getName($locale),
					$productPrice->getPriceWithoutVat(),
					$productPrice->getPriceWithVat(),
					$item->getVat()->getPercent(),
					$quantifiedItem->getQuantity(),
					$item
				);
			} else {
				$message = 'Object "' . get_class($item) . '" is not valid for OrderItem.';
				throw new \SS6\ShopBundle\Model\Order\Item\Exception\InvalidQuantifiedItemException($message);
			}

			$order->addItem($orderItem);
		}
	}

}
