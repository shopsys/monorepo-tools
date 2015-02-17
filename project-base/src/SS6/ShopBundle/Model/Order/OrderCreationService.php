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
use SS6\ShopBundle\Model\Order\Preview\OrderPreview;
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
	 * @param \SS6\ShopBundle\Model\Order\Preview\OrderPreview $orderPreview
	 */
	public function fillOrderItems(Order $order, OrderPreview $orderPreview) {
		$locale = $this->domain->getDomainConfigById($order->getDomainId())->getLocale();

		$this->fillOrderProducts($order, $orderPreview, $locale);
		$this->fillOrderTransportAndPayment($order, $orderPreview, $locale);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Model\Order\Preview\OrderPreview $orderPreview
	 * @param string $locale
	 */
	private function fillOrderTransportAndPayment(Order $order, OrderPreview $orderPreview, $locale) {
		$payment = $order->getPayment();
		$paymentPrice = $this->paymentPriceCalculation->calculatePrice(
			$payment,
			$order->getCurrency(),
			$orderPreview->getProductsPrice(),
			$order->getDomainId()
		);
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
		$transportPrice = $this->transportPriceCalculation->calculatePrice(
			$transport,
			$order->getCurrency(),
			$orderPreview->getProductsPrice(),
			$order->getDomainId()
		);
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
	 * @param \SS6\ShopBundle\Model\Order\Preview\OrderPreview $orderPreview
	 * @param string $locale
	 */
	private function fillOrderProducts(Order $order, OrderPreview $orderPreview, $locale) {
		$quantifiedItemPrices = $orderPreview->getQuantifiedItemsPrices();

		foreach ($orderPreview->getQuantifiedItems() as $index => $quantifiedItem) {
			$product = $quantifiedItem->getItem();
			if (!$product instanceof Product) {
				$message = 'Object "' . get_class($product) . '" is not valid for order creation.';
				throw new \SS6\ShopBundle\Model\Order\Item\Exception\InvalidQuantifiedItemException($message);
			}

			$quantifiedItemPrice = $quantifiedItemPrices[$index];
			/* @var $quantifiedItemPrice \SS6\ShopBundle\Model\Order\Item\QuantifiedItemPrice */

			$orderItem = new OrderProduct(
				$order,
				$product->getName($locale),
				$quantifiedItemPrice->getUnitPriceWithoutVat(),
				$quantifiedItemPrice->getUnitPriceWithVat(),
				$product->getVat()->getPercent(),
				$quantifiedItem->getQuantity(),
				$product
			);

			$order->addItem($orderItem);
		}
	}

}
